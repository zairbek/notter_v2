<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\V1\Auth\AuthBaseController;
use App\User;
use Carbon\Carbon;

class VerificationController extends AuthBaseController
{
    public function verify(User $user)
    {
        $user->email_verified_at = Carbon::now();
        $user->save();
    }
}
