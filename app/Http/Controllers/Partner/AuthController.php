<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ForgotPasswordRequest;
use App\Http\Requests\Partner\LoginLinkRequest;
use App\Http\Requests\Partner\LoginRequest;
use App\Http\Requests\Partner\ResetPasswordRequest;
use App\Services\Partner\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    /**
     * Display the login view or redirect to partner index page if already logged in.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function login(Request $request): View|RedirectResponse
    {
        $email = $request->get('e', null);
        $password = $request->get('p', null);

        if ($email && $password) {
            $email = Crypt::decryptString($email);
            $password = Crypt::decryptString($password);
        }

        // Redirect to partner index page if already logged in, otherwise show login view
        return auth()->guard('partner')->check()
            ? redirect()->intended(route('partner.index'))
            : view('partner.auth.login', compact('email', 'password'));
    }

    /**
     * Authenticate user and log them in.
     */
    public function postLogin(LoginRequest $request, AuthService $authService): RedirectResponse
    {
        // Get validated request fields
        $input = $request->validated();

        // Login
        $success = $authService->login($input);

        return ($success)
            ? redirect()->intended(route('partner.index'))
            : redirect()->route('partner.login')->with('error', trans('common.login_not_recognized'))->withInput($request->except('password'));

        // Login link
        /*
        $authService->sendLoginLink($input);

        return view('partner.auth.link-sent', [
            'email' =>  $request->email
        ]);
        */
    }

    /**
     * Display the forgot password view or redirect to partner index page if already logged in.
     */
    public function forgotPassword(Request $request): View|RedirectResponse
    {
        return (auth()->guard('partner')->check())
            ? redirect()->route('partner.index')
            : view('partner.auth.forgot-password');
    }

    /**
     * Send password reset link to the user's email.
     */
    public function postForgotPassword(ForgotPasswordRequest $request, AuthService $authService): RedirectResponse
    {
        // Get validated request fields
        $input = $request->validated();

        // Send reset link
        $success = $authService->sendResetPasswordLink($input['email']);

        return ($success)
            ? redirect()->route('partner.forgot_password')->with('success', trans('common.reset_link_has_been_sent_to_email', ['email' => '<u>'.$input['email'].'</u>']))
            : redirect()->route('partner.forgot_password')->with('error', trans('common.user_not_found'))->withInput();
    }

    /**
     * Display the reset password view or redirect to partner index page if already logged in.
     */
    public function resetPassword(Request $request): View|RedirectResponse
    {
        // Post reset link
        $postResetLink = URL::temporarySignedRoute(
            'partner.reset_password.post',
            now()->addMinutes(120),
            [
                'email' => $request->email,
            ]
        );

        return (auth()->guard('partner')->check())
            ? redirect()->route('partner.index')
            : view('partner.auth.reset-password', compact('postResetLink'));
    }

    /**
     * Update the user's password.
     */
    public function postResetPassword(ResetPasswordRequest $request, AuthService $authService): RedirectResponse
    {
        // Get validated request fields
        $input = $request->validated();

        // Send reset link
        $success = $authService->updatePassword($input['email'], $input['password']);

        return ($success)
            ? redirect()->route('partner.login')->with('success', trans('common.login_with_new_password'))->withInput(['email' => $input['email']])
            : redirect($request->getRequestUri())->with('error', trans('common.unknown_error'));
    }

    /**
     * Log in an partner user using a login link.
     */
    public function loginLink(LoginLinkRequest $request, AuthService $authService): RedirectResponse
    {
        if (! auth()->guard('partner')->check()) {
            $user = $authService->login($request->email);
        } else {
            $user = auth()->guard('partner')->user();
        }

        $redir = $request->intended;

        return $user
            ? redirect($redir)
            : redirect()->route('partner.login');
    }

    /**
     * Log out an partner user and redirect to the login page.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('partner')->logout();

        $request->session()->flash('success', trans('common.logout_success'));

        return redirect()->route('partner.login');
    }
}
