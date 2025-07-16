<?php

declare(strict_types=1);

namespace App\Domains\System\Auth;

use App\Core\Authentication\AuthenticationService;
use App\Core\Authentication\InvalidCredentialsException;
use App\Core\Authentication\TooManyAuthAttemptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class SystemAuthController extends AuthenticationService
{
    public function __construct()
    {
        $this->setGuard('system');
    }

    public function systemLoginPage()
    {
        return Inertia::render('v1/auth/Login');
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function processSystemSignIn(SystemLoginRequest $request): JsonResponse
    {
        try {
            $this->authenticate($request);


            return successResponseJson([
                'message' => 'Successfully logged in. Welcome Back! '.auth()?->user()?->email,
                'data' => [
                    'redirect_to' => route('dashboard'),
                ]
            ]);
        } catch (TooManyAuthAttemptException|InvalidCredentialsException $e) {
            return errorResponseJson(['message' => $e->getMessage()],
                $e->getStatusCode() ?? 403
            );
        }
    }

    public function processSystemSignOut(Request $request): JsonResponse
    {
        $this->logout($request);

        return successResponseJson([
            'message' => 'Successfully logged out.',
            'redirect_to' => route('get.system_login'),
        ]);
    }

    public function manageContextAfterLogin(Request $request): void
    {
        $request->session()->forget('previous_login_context');
        $request->session()->put('login_context', $this->guard);
    }

    public function mangeContextAfterLogout(Request $request): void
    {
        $request->session()->forget('login_context');
        $request->session()->put('previous_login_context', $this->guard);
    }
}
