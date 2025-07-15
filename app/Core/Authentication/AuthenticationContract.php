<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use Illuminate\Http\Request;

interface AuthenticationContract
{
    public function authenticate(Request $request);

    public function logout(Request $request);

    public function manageContextAfterLogin(Request $request);

    public function mangeContextAfterLogout(Request $request);
}
