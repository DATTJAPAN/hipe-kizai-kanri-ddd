<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Auth;

use App\Core\Authentication\AuthenticationService;
use App\Core\Authentication\InvalidCredentialsException;
use App\Core\Authentication\TooManyAuthAttemptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationAuthController extends AuthenticationService
{
    public function __construct()
    {
        $this->setGuard('web');
        inertia()->share('context', ['scope' => $this->guard]);

    }

    public function getLogin(): Response
    {
        return Inertia::render('v1/auth/Login');
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function postLogin(OrganizationLoginRequest $request): JsonResponse
    {
        try {
            $this->authenticate($request);

            return successResponseJson(['message' => 'Successfully logged in. Welcome Back! '.auth()?->user()?->email]);
        } catch (TooManyAuthAttemptException|InvalidCredentialsException $e) {
            return errorResponseJson(['message' => $e->getMessage()],
                httpStatus: $e->getStatusCode() ?? 403
            );
        }
    }

    public function postLogOut(Request $request): JsonResponse
    {
        $this->logout($request);

        return successResponseJson([
            'message' => 'Successfully logged out.',
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
