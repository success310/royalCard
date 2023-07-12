<?php

namespace App\Services\Partner;

use App\Notifications\Partner\ResetPassword;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class AuthService
{
    /**
     * Retrieve active partner with login credentials.
     *
     * @param  array  $login Login credentials.
     * @return object|null Partner object if active, otherwise null.
     */
    public function login(array $login)
    {
        $partnerService = resolve('App\Services\Partner\PartnerService');
        $partner = $partnerService->findActiveByEmail($login['email']);

        $authenticated = false;

        if ($partner) {
            if (Hash::check($login['password'], $partner->password)) {
                if ($partner->is_active == 1) {
                    if (! $partner->email_verified_at) {
                        $partner->email_verified_at = Carbon::now('UTC');
                    }

                    // Update login stats
                    $partner->number_of_times_logged_in = $partner->number_of_times_logged_in + 1;
                    $partner->last_login_at = Carbon::now('UTC');
                    $partner->save();

                    // Successfully authenticated
                    $authenticated = true;
                }
            }
        }

        if ($authenticated) {
            Auth::guard('partner')->login($partner, (bool) $login['remember']);

            return $partner;
        } else {
            return null;
        }
    }

    /**
     * Send login link.
     *
     * @param  array  $login Login credentials.
     * @return string Partner email address.
     */
    public function sendLoginLink(array $login): string
    {
        // Login link
        $url = URL::temporarySignedRoute(
            'partner.login.link',
            now()->addMinutes(30),
            [
                'email' => $login['email'],
                'intended' => redirect()->intended(route('partner.index'))->getTargetUrl(),
            ]
        );

        // Send login mail
        Notification::route('mail', $login['email'])
            ->notify(new Login($url));

        return $login['email'];
    }

    /**
     * Send link to reset password.
     *
     * @param  string  $email Email address.
     * @return object|null Partner object if active and found, otherwise null.
     */
    public function sendResetPasswordLink(string $email)
    {
        $partnerService = resolve('App\Services\Partner\PartnerService');
        $partner = $partnerService->findActiveByEmail($email);

        if ($partner) {
            // Reset link
            $resetLink = URL::temporarySignedRoute(
                'partner.reset_password',
                now()->addMinutes(120),
                [
                    'email' => $email,
                ]
            );

            // Send reset link
            $partner->notify(new ResetPassword($resetLink));
        }

        return $partner;
    }

    /**
     * Update password.
     *
     * @param  string  $email Email address.
     * @param  string  $password New password.
     * @return object|null Partner object if active and found, otherwise null.
     */
    public function updatePassword(string $email, string $password)
    {
        $partnerService = resolve('App\Services\Partner\PartnerService');
        $partner = $partnerService->findActiveByEmail($email);

        if ($partner) {
            $partner->password = bcrypt($password);
            $partner->save();
        }

        return $partner;
    }
}
