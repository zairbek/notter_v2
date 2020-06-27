<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Notifications\VerifyEamilNotification;
use App\User;
use Illuminate\Http\Request;

class SignUpController extends AuthBaseController
{
    /**
     * @OA\Post(
     *     path="/auth/sign-up",
     *     tags={"Authentication"},
     *     summary="Регистрация",
     *     operationId="signUp",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/SignUp")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse"),
     *         @OA\Header(header="Access-Token", ref="#/components/headers/Access-Token"),
     *         @OA\Header(header="Refresh-token", ref="#/components/headers/Refresh-token"),
     *         @OA\Header(header="Token-Type", ref="#/components/headers/Token-Type"),
     *         @OA\Header(header="Expires-In", ref="#/components/headers/Expires-In"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/SignUpErrorsSchema"),
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signUp(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = $request->password;
        $user->save();

        $user->notify(new VerifyEamilNotification($user));

        return $this->authenticated($request, $user);
    }

}
