<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hashing the password
        ]);

        return $this->sendResponse($user, 'User registered successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('auth_token')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }
    }
    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
            return $this->sendResponse(null, 'Logged out successfully.');
        } else {
            return $this->sendError('Unauthenticated.', ['error' => 'No active user found'], 401);
        }
    }

    /**
     * Forgot Password api
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();
        // Send reset link
        $status = Password::sendResetLink(
            $validated
        );
        return $status == Password::RESET_LINK_SENT
            ? $this->sendResponse(null, 'If the email exists, a reset link will be sent.')
            : $this->sendError('Unable to send reset link at the moment. Please try again later.', [], 500);
    }
    /**
     * Reset Password api
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validatedData = $request->validated();

        $status = Password::reset(
            $validatedData,
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->sendResponse(null, 'Password reset successfully.')
            : $this->sendError('BadRequest.', ['error' => __($status)], 400);
    }
}
