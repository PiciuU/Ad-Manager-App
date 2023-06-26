<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Models\PasswordResetToken;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

use Carbon\Carbon;

class UserController extends Controller
{
    private $logController;

    public function __construct(LogController $logController = new LogController())
    {
        $this->logController = $logController;
    }

    /**
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * =====================
     *     ADMIN SECTION
     * =====================
     */

    /**
     * Retrieve a list of users.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $users = new UserCollection(User::all());

        $users->returnFields(['id', 'login']);

        return $this->successResponse("Users has been successfully found.", $users);
    }

    /**
     * Retrieve a specific user by its ID.
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($id);

        if (!$user) return $this->errorResponse("User not found.", 404);

        return $this->successResponse("User has been successfully found.", new UserResource($user));
    }

    /**
     * Store a new uer.
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        if (!$user) return $this->errorResponse("An error occurred while creating the user, try again later.", 500);

        return $this->successResponse("User has been successfully created.", new UserResource($user));
    }

    /**
     * Update an existing user.
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = new UserResource(User::find($id));

        if (!$user->update($request->validated())) return $this->errorResponse("An error occurred while updating the user, try again later", 500);

        return $this->successResponse("User has been successfully updated", new UserResource($user));
    }

    /**
     * Generates a 32-character activation key using the MD5 cryptographic function.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function generateActivationKey()
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $key = md5(time().rand());

        return $this->successResponse("Activation key has been successfully generated.", $key);
    }

    /**
     * Assign to user account a 32-character activation key.
     * This method is accessible only to administrators.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function assignActivationKey(UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($request->validated()['id']);

        if (!$user) return $this->errorResponse("User not found.", 404);

        $updateData = [
            'activation_key' => $request->validated()['activation_key'],
        ];

        if (!$user->update($updateData)) return $this->errorResponse("An error occurred while updating the user data, try again later.", 500);

        return $this->successResponse("Activation key has been successfully assigned.", new UserResource($user));
    }

    /**
     * Ban or unban user account
     * This method is accessible only to administrators.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function toggleBan(UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($request->validated()['id']);

        if (!$user) return $this->errorResponse("User not found.", 404);

        if ($user->is_banned) {
            $updateData = [
                'is_banned' => false,
                'ban_reason' => '',
            ];
        } else {
            $updateData = [
                'is_banned' => true,
                'ban_reason' => $request->validated()['ban_reason'] ?? "Nie podano powodu.",
            ];
        }

        if (!$user->update($updateData)) return $this->errorResponse("An error occurred while banning the user, try again later.", 500);

        return $this->successResponse("User has been successfully banned.", new UserResource($user));
    }

    /**
     * Update the password of the user.
     * This method is accessible only to administrators.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function changePassword(UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($request->validated()['id']);

        if (!$user) return $this->errorResponse("User not found.", 404);

        $updateData = [
            'password' => Hash::make($request->validated()['password'])
        ];

        if (!$user->update($updateData)) return $this->errorResponse("An error occurred while changing the user password, try again later.", 500);

        return $this->successResponse("User password has been successfully changed.");
    }

    /**
     * =====================
     *     USER SECTION
     * =====================
     */

    /**
     * Validate the authentication key of an account.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function validateAuthenticationKey(UserRequest $request)
    {
        $user = User::where('activation_key', $request->validated()['activation_key'])->first();

        if (!$user) return $this->errorResponse("Invalid activation key.", 401);

        return $this->successResponse("Activation key has been successfully verified.", ['login' => $user->login, 'email' => $user->email]);
    }

    /**
     * Validate the login of an account.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function validateLogin(UserRequest $request)
    {
        return $this->successResponse("Login has been successfully verified.");
    }

    /**
     * Validate the email of an account.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function validateEmail(UserRequest $request)
    {
        return $this->successResponse("Email has been successfully verified.");
    }

     /**
     * Activate user account and log user into system if successful.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function activateAccount(UserRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::where('activation_key', $validatedData['activation_key'])->first();

        if (!$user) return $this->errorResponse("Invalid activation key.", 401);

        $validatedData['activated_at'] = date("Y-m-d H:i:s");
        $validatedData['activation_key'] = null;
        $validatedData['password'] = Hash::make($validatedData['password']);

        if(!$user->update($validatedData)) return $this->errorResponse("An error occurred while activating the user account, try again later.", 500);

        $this->logController->createLogEntry('AUTH/ACTIVATE', ['user_id' => $user->id]);

        $credentials = [
            'login' => $request->validated()['login'],
            'password' => $request->validated()['password']
        ];

        if (Auth::attempt($credentials)) {
            $user = new UserResource(Auth::user());

            if ($user->is_banned) return $this->successResponse("Your account has been suspended.", $user->ban_reason, 403);

            $authToken = $user->createToken('basic-token', ['basic']);

            return $this->successResponse("User has been activated successfully.", ['user' => $user, 'token' => $authToken->plainTextToken]);
        }

        return $this->successResponse("User has been activated successfully.");
    }

    /**
     * Log in the user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function login(UserRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = new UserResource(Auth::user());

            if ($user->is_banned) return $this->successResponse("Your account has been suspended.", $user->ban_reason, 403);

            if ($user->isAdmin()) $authToken = $user->createToken('admin-token', ['admin']);
            else $authToken = $user->createToken('basic-token', ['basic']);

            $this->logController->createLogEntry('AUTH/LOGIN', ['user_id' => $user->id]);

            return $this->successResponse("Login successful.", ['user' => $user, 'token' => $authToken->plainTextToken]);
        }

        return $this->errorResponse("Invalid username or password.", 401);
    }

    /**
     * Logout user by destroying their current token.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        $this->logController->createLogEntry('AUTH/LOGOUT', ['user_id' =>auth()->user()->id]);

        return $this->successResponse("Logout successful.");
    }

    /**
     * Logout user by destroying all their tokens.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function forceLogout() {
        auth()->user()->tokens()->delete();

        $this->logController->createLogEntry('AUTH/LOGOUT', ['user_id' =>auth()->user()->id]);

        return $this->successResponse("Logout successful.");
    }

    /**
     * Get the data of the authenticated user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function userData()
    {
        $user = new UserResource(auth()->user());
        return $this->successResponse("User has been successfully fetched.", $user);
    }

    /**
     * Recover password for the user by generating a reset password hash
     * This method generates a reset password hash for the user, which allows them to recover their password.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function recover(UserRequest $request)
    {
        $user = User::where('email', $request->validated()['email'])->first();

        if (!$user) return $this->successResponse("If an account is associated with the provided email address, we have sent a message to it.");

        $resetPassword = PasswordResetToken::where('user_id', $user->id)->first();
        if ($resetPassword) $resetPassword->delete();

        $resetPasswordHash = md5(rand().time());

        PasswordResetToken::create([
            'user_id' => $user->id,
            'hash' => $resetPasswordHash,
            'valid_until' => Carbon::now()->addDay()->format('Y-m-d H:i:s')
        ]);

        // Mail::to($user->email)->send(new ResetPasswordRequestMail($resetPasswordHash));

        // if (Mail::failures()) return $this->errorResponse("An error occurred while sending email, try again later.", 500);

        return $this->successResponse("If an account is associated with the provided email address, we have sent a message to it.");
    }

    /**
     * Verify password recovery.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function recoverToken($hash)
    {
        $resetPassword = PasswordResetToken::where('hash', $hash)
                        ->whereDate('valid_until', '>', now())
                        ->first();

        if (!$resetPassword) return $this->errorResponse("Invalid or expired password reset token.");

        return $this->successResponse("Valid password reset token.");
    }

    /**
     * Update user password with password recovery.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function resetPassword(UserRequest $request)
    {
        $validatedData = $request->validated();

        $resetPassword = PasswordResetToken::where('hash', $validatedData['hash'])
                        ->whereDate('valid_until', '>', now())
                        ->first();

        if (!$resetPassword) return $this->errorResponse("Invalid or expired password reset token.");

        $user = User::where('id', $resetPassword->user_id)->first();

        if (!$user) return $this->errorResponse("Invalid or expired password reset token.");

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        $resetPassword->delete();

        // Mail::to($advertiser['email'])->send(new ResetPasswordConfirmationMail());

        $this->logController->createLogEntry('AUTH/PASSWORD/RESET', ['user_id' => $user->id]);

        return $this->successResponse("Your password has been successfully reset.");
    }

    /**
     * Update the data of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateData(UserRequest $request)
    {
        $user = auth()->user();

        if (!$user->update($request->validated())) return $this->errorResponse("An error occurred while updating the user, try again later.", 500);

        $this->logController->createLogEntry('AUTH/UPDATE', ['user_id' => $user->id]);

        return $this->successResponse("User has been successfully updated.", new UserResource($user));
    }

    /**
     * Update the email of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateMail(UserRequest $request)
    {
        $user = auth()->user();

        if(!$user->update($request->validated())) return $this->errorResponse("An error occurred while updating the user data, try again later.", 500);

        $this->logController->createLogEntry('AUTH/MAIL', ['user_id' => $user->id]);

        return $this->successResponse("User has been successfully updated.", new UserResource($user));
    }

    /**
     * Update the password of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updatePassword(UserRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->validated()['password_current'], $user->password)) {
            return $this->errorResponse("Invalid current password.", 400);
        }

        if ($request->validated()['password'] === $request->validated()['password_current']) {
            return $this->errorResponse("New password must be different from the current password.", 400);
        }

        if (!$user->update([
            'password' => Hash::make($request->validated()['password'])
        ])) return $this->errorResponse("An error occurred while updating the password, try again later.", 500);


        $this->logController->createLogEntry('AUTH/PASSWORD/CHANGE', ['user_id' => $user->id]);

        return $this->successResponse("User has been successfully updated.");
    }
}
