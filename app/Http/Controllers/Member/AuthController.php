<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\ForgotPasswordRequest;
use App\Http\Requests\Member\LoginLinkRequest;
use App\Http\Requests\Member\LoginRequest;
use App\Http\Requests\Member\RegistrationRequest;
use App\Http\Requests\Member\ResetPasswordRequest;
use App\Services\Member\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

/**
 * Class AuthController
 *
 * Handles authentication related tasks for the members.
 */
class AuthController extends Controller
{
    /**
     * Display the login view or redirect to the homepage if already logged in.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Extract the current URL and parse it into segments.
        $url = url()->previous();
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $guardOrItemSegment = $segments[2] ?? null;

        // Check if the segment is equal to 'card'.
        // If it is, store the previous URL in the session under 'from.member'.
        // This allows for redirection back to the previous page after login.
        if ($guardOrItemSegment == 'card') {
            session()->put('from.member', $url);
        }

        // The 'from' query string parameter might be present in a request.
        // If it is, override the session 'from.member'.
        $from = $request->get('from', null);
        if ($from) {
            session()->put('from.member', urldecode($from));
        }

        // Check if the user is already logged in as a member
        $isLoggedIn = auth()->guard('member')->check();

        // If the user is logged in, redirect them to the homepage
        if ($isLoggedIn) {
            return redirect()->route('member.dashboard');
        }

        $email = $request->get('e', null);
        $password = $request->get('p', null);

        if ($email && $password) {
            $email = Crypt::decryptString($email);
            $password = Crypt::decryptString($password);
        
            $credentials = [
                'email' => $email,
                'password' => $password,
                'remember' => 1
            ];
        
            // Validation rules, assuming it is similar to your LoginRequest
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
        
            // Validate the data
            $validator = Validator::make($credentials, $rules);
        
            // If validation fails, redirect back with errors
            if ($validator->fails()) {
                return back()->withErrors($validator);
            }
        
            // Instantiate AuthService
            $authService = app(AuthService::class);
        
            return $this->attemptLogin($credentials, $authService);
        }

        // If the user is not logged in, show the login view
        return view('member.auth.login', compact('email', 'password'));
    }

    /**
     * Attempt to authenticate a user with given credentials.
     * Redirects to an appropriate location based on the authentication result.
     *
     * @param array $credentials Key-value pairs representing user credentials, typically ['email' => 'user@example.com', 'password' => 'secret']
     * @param AuthService $authService An instance of AuthService to perform authentication logic
     * @return \Illuminate\Http\RedirectResponse Redirect response towards the appropriate location based on the authentication result
     */
    public function attemptLogin(array $credentials, AuthService $authService)
    {
        // Use the AuthService to attempt to authenticate the user with the provided credentials
        // If the authentication is successful, an instance of the authenticated user is returned; otherwise, null is returned
        $authenticatedUser = $authService->login($credentials);

        // If the user is authenticated successfully, determine where they should be redirected
        if ($authenticatedUser !== null) {
            // Redirect the user to the determined route
            $sessionFrom = session()->get('from.member', null);
            $redirectRoute = $sessionFrom ?? route('member.index');

            // If this is the user's first login, change the redirection route
            if($authenticatedUser->number_of_times_logged_in == 1 && $sessionFrom === null) {
                $redirectRoute = route('member.dashboard');
            }

            return redirect()->intended($redirectRoute);
        } else {
            // If the authentication attempt was not successful, redirect the user back to the login page
            // Also, flash an error message to the session, and re-populate the old input except the password
            return redirect()->route('member.login')
                            ->with('error', trans('common.login_not_recognized'))
                            ->withInput(Arr::except($credentials, 'password'));
        }
    }

    /**
     * Authenticate user and log them in.
     *
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(LoginRequest $request, AuthService $authService)
    {
        return $this->attemptLogin($request->validated(), $authService);

        // Login link
        /*
        $authService->sendLoginLink($input);

        return view('member.auth.link-sent', [
            'email' =>  $request->email
        ]);
        */
    }

    /**
     * Display the registration view or redirect to the homepage if already logged in.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Check if the user is already logged in as a member
        $isLoggedIn = auth()->guard('member')->check();

        // If the user is logged in, redirect them to the homepage
        if ($isLoggedIn) {
            return redirect()->route('member.dashboard');
        }

        // If the user is not logged in, show the registration view
        return view('member.auth.register');
    }

    /**
     * Register a new user and handle the registration request.
     *
     * @param RegistrationRequest $request
     * @param AuthService $authService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRegister(RegistrationRequest $request, AuthService $authService)
    {
        // Get validated input fields from the request object
        $input = $request->validated();

        // Register the user using the AuthService
        $success = $authService->register($input);

        // If registration is successful, redirect to the registration page with a success message
        // Otherwise, redirect back to the registration page with an error message
        return $success
            ? redirect()->route('member.register')->with('success', trans('common.registration_success', ['email' => '<u>' . $input['email'] . '</u>']))
            : redirect()->route('member.register')->with('error', trans('common.unknown_error'));
    }

    /**
     * Display the forgot password view or redirect to the homepage if already logged in.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        // Check if the user is already logged in as a member
        $isLoggedIn = auth()->guard('member')->check();

        // If the user is logged in, redirect them to the homepage
        if ($isLoggedIn) {
            return redirect()->route('member.dashboard');
        }

        // If the user is not logged in, show the forgot password view
        return view('member.auth.forgot-password');
    }

    /**
     * Send a password reset link to the user's email and handle the forgot password request.
     *
     * @param ForgotPasswordRequest $request
     * @param AuthService $authService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postForgotPassword(ForgotPasswordRequest $request, AuthService $authService)
    {
        // Get validated input fields from the request object
        $input = $request->validated();

        // Send a password reset link to the user's email using the AuthService
        $success = $authService->sendResetPasswordLink($input['email']);

        // If the reset link is sent successfully, redirect to the forgot password page with a success message
        // Otherwise, redirect back to the forgot password page with an error message and input fields
        return $success
            ? redirect()->route('member.forgot_password')->with('success', trans('common.reset_link_has_been_sent_to_email', ['email' => '<u>' . $input['email'] . '</u>']))
            : redirect()->route('member.forgot_password')->with('error', trans('common.user_not_found'))->withInput();
    }

    /**
     * Display the reset password view or redirect to the homepage if already logged in.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        // Create a temporary signed route for the password reset request
        // The route will be valid for 120 minutes
        $postResetLink = URL::temporarySignedRoute(
            'member.reset_password.post',
            now()->addMinutes(120),
            ['email' => $request->email]
        );

        // Check if the user is already logged in as a member
        $isLoggedIn = auth()->guard('member')->check();

        // If the user is logged in, redirect them to the homepage
        if ($isLoggedIn) {
            return redirect()->route('member.dashboard');
        }

        // If the user is not logged in, show the reset password view with the temporary signed route
        return view('member.auth.reset-password', compact('postResetLink'));
    }

    /**
     * Update the user's password and handle the reset password request.
     *
     * @param ResetPasswordRequest $request
     * @param AuthService $authService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postResetPassword(ResetPasswordRequest $request, AuthService $authService)
    {
        // Get validated input fields from the request object
        $input = $request->validated();

        // Update the user's password using the AuthService
        $success = $authService->updatePassword($input['email'], $input['password']);

        // If the password update is successful, redirect to the login page with a success message and the user's email
        // Otherwise, redirect back to the reset password page with an error message
        return $success
            ? redirect()->route('member.login')->with('success', trans('common.login_with_new_password'))->withInput(['email' => $input['email']])
            : redirect($request->getRequestUri())->with('error', trans('common.unknown_error'));
    }

    /**
     * Log in a user using a login link.
     *
     * @param LoginLinkRequest $request
     * @param AuthService $authService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginLink(LoginLinkRequest $request, AuthService $authService)
    {
        // Check if the user is not logged in as a member
        // If not, log in the user using the AuthService
        // If the user is already logged in, get the current user object
        $user = !auth()->guard('member')->check() ? $authService->login($request->email) : auth()->guard('member')->user();

        // Get the intended redirect URL
        $redir = $request->intended;

        // If the user is logged in, redirect to the intended URL
        // Otherwise, redirect to the login page
        return $user ? redirect($redir) : redirect()->route('member.login');
    }

    /**
     * Log out a user and redirect to the index page.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Log out the user using the 'member' guard
        Auth::guard('member')->logout();

        // Add a success message to the session flash data
        $request->session()->flash('success', trans('common.logout_success'));

        // Redirect to the member index page
        return redirect()->route('member.index');
    }
}
