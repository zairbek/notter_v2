<?php
namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;

class SignInController extends AuthBaseController
{
    /**
     * @OA\Post(
     *     path="/auth/sign-in",
     *     tags={"Authentication"},
     *     summary="Авторизация",
     *     operationId="signIn",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/SignIn")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse"),
     *         @OA\Header(header="Access-Token", ref="#/components/headers/Access-Token"),
     *         @OA\Header(header="Refresh-token", ref="#/components/headers/Refresh-token"),
     *         @OA\Header(header="Token-Type", ref="#/components/headers/Token-Type"),
     *         @OA\Header(header="Expires-In", ref="#/components/headers/Expires-In"),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/SignInErrorsSchema"),
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signIn(Request $request)
    {
        $this->validateLogin($request);
        if ($this->attemptLogin($request)) {
            return $this->authenticated($request, auth()->user());
        }

        return $this->sendFailedLoginResponse($request);
    }

}
