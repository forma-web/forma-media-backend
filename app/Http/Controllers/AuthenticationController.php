<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegistrationUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * @param \App\Http\Requests\RegistrationUserRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function register(RegistrationUserRequest $request): UserResource
    {
        $credentials = collect($request->validated());

        $user = User::create(
            $credentials
                ->replaceByKey('password', fn($p) => Hash::make($p))
                ->all()
        );

        /** @var string $token */
        $token = auth()->login($user);

        event(new Registered($user));

        return $this->current()->additional([
            'meta' => $this->withToken($token)
        ]);
    }

    /**
     * @param \App\Http\Requests\LoginUserRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function login(LoginUserRequest $request): UserResource
    {
        $credentials = collect($request->validated());

        abort_if(
            !$token = auth()->attempt($credentials->all()),
            401,
            'Unauthorized'
        );

        return $this->current()->additional([
            'meta' => $this->withToken($token)
        ]);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();

        response()->noContent();
    }

    /**
     * @return \App\Http\Resources\UserResource
     */
    public function current(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): JsonResponse
    {
        /** @var string $token */
        $token = auth()->refresh();

        return response()->json([
            'meta' => $this->withToken($token)
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Auth\EmailVerificationRequest $request
     * @return void
     */
    public function verify(EmailVerificationRequest $request): void
    {
        $request->fulfill();

        response()->noContent();
    }

    /**
     * @return void
     */
    public function resend(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasVerifiedEmail())
            auth()->user()->sendEmailVerificationNotification();

        response()->noContent();
    }

    /**
     * @param string $token
     * @return array
     */
    private function withToken(string $token): array
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}