<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

abstract class AuthenticationService implements AuthenticationContract
{
    protected string $guard = 'web';

    protected AuthManager|SessionGuard|Auth $auth;

    protected int $loginMaxAttempts = 5;

    protected bool $authenticated = true;

    public function setGuard(string $guard): void
    {
        $this->guard = $guard;
        $this->auth = Auth::guard(name: $this->guard);
    }

    public function setLoginMaxAttempts(int $attempts): void
    {
        $this->loginMaxAttempts = $attempts;
    }

    /**
     * @throws InvalidCredentialsException|TooManyAuthAttemptException
     */
    public function authenticate(Request $request): bool
    {
        try {
            $this->ensureIsNotRateLimited($request);

            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (! $this->auth->attempt(credentials: $credentials, remember: $remember)) {
                // Failed Sign In
                // --> log the attempt
                // --> throw
                RateLimiter::hit($this->throttleKey($request));
                $this->authenticated = false;
                throw new InvalidCredentialsException();
            }

            // Success Sign In
            // --> Clear the log attempt
            RateLimiter::clear($this->throttleKey($request));

            // Add additional context during login
            $this->manageContextAfterLogin($request);

            $request->session()->regenerate();

            return $this->authenticated;

        } catch (TooManyAuthAttemptException|Exception $e) {
            $this->authenticated = false;
            throw $e;
        }
    }

    public function logout(Request $request): void
    {
        $this->auth->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Remove the context that was added during login
        $this->mangeContextAfterLogout($request);
    }

    /**
     * @throws TooManyAuthAttemptException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $throttleKey = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($throttleKey, $this->loginMaxAttempts)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($throttleKey);

        throw TooManyAuthAttemptException::make($seconds);
    }

    protected function throttleKey(Request $request): string
    {
        $string = $request->string('email') ?? $request->string('username');
        $ip = $request->ip();

        return str("{$string}|{$ip}")
            ->lower()
            ->transliterate()
            ->toString();
    }
}
