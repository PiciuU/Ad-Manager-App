<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ad;

use App\Models\Log;
use App\Http\Requests\LogRequest;
use App\Http\Resources\LogCollection;
use App\Http\Resources\LogResource;

class LogController extends Controller
{

     const OPERATIONS = [
        'AUTH/ACTIVATE' => 'Użytkownik aktywował swoje konto.',
        'AUTH/LOGIN' => 'Użytkownik zalogował się.',
        'AUTH/LOGOUT' => 'Użytkownik wylogował się.',
        'AUTH/UPDATE' => 'Użytkownik zaktualizował swoje dane.',
        'AUTH/MAIL' => 'Użytkownik zaktualizował swój adres email.',
        'AUTH/PASSWORD/CHANGE' => 'Użytkownik zaktualizował swoje hasło.',
        'AUTH/PASSWORD/RESET' => 'Użytkownik zresetował swoje hasło.',
        'AD/CREATE' => 'Użytkownik utworzył nową reklamę.',
        'AD/UPDATE' => 'Użytkownik zaktualizował swoją reklamę.',
        'AD/DEACTIVATE' => 'Użytkownik zdezaktywował swoją reklamę.',
        'AD/RENEW' => 'Użytkownik odnowił swoją reklamę.',
        'INVOICE/PAYMENT' => 'Użytkownik opłacił swoją reklamę.',

        'ADMIN/AD/CREATE' => 'Administrator utworzył reklamę dla użytkownika.',
        'ADMIN/AD/UPDATE' => 'Administrator zaktualizował reklamę użytkownika.',
        'ADMIN/AD/DEACTIVATE' => 'Administrator zdezaktywował reklamę użytkownika.',
        'ADMIN/AD/RENEW' => 'Administrator odnowił reklamę użytkownika.',
        'ADMIN/INVOICE/PAYMENT' => 'Administrator opłacił reklamę użytkownika.',
        'ADMIN/INVOICE/CREATE' => "Administrator ręcznie utworzył fakturę dla reklamy użytkownika."
    ];

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
     * Create a new log entry for a specific operation.
     * This method is internal and should only be called by an instance of LogController.
     *
     * @param string $operation
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    public function createLogEntry($operation, $data = [])
    {
        if (!isset(self::OPERATIONS[$operation])) return false;

        $log = Log::create([
            'user_id' => $data['user_id'] ?? auth()->user()->id,
            'ad_id' => $data['ad_id'] ?? null,
            'operation_tags' => $operation,
            'message' => $data['message'] ?? self::OPERATIONS[$operation],
            'notes' => $data['notes'] ?? null,
        ]);

        return true;
    }

    /**
     * Retrieve a paginated list of logs.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $logs = Log::orderBy('created_at', 'desc')->paginate(15);

        $responseData = [
            'current_page' => $logs->currentPage(),
            'entries' => new LogCollection($logs->items()),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
        ];

        return $this->successResponse("Logs has been successfuly found.", $responseData);
    }

    /**
     * Retrieve a paginated list of logs for specified user
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function showUser($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($id);

        if (!$user) return $this->errorResponse("User not found.", 404);

        $logs = $user->logs()->orderBy('created_at', 'desc')->paginate(10);

        $responseData = [
            'current_page' => $logs->currentPage(),
            'entries' => new LogCollection($logs->items()),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
        ];

        return $this->successResponse("Logs has been successfully found.", $responseData);
    }

    /**
     * Retrieve a paginated list of logs for specified ad
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function showAd($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::find($id);

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $logs = $ad->logs()->orderBy('created_at', 'desc')->paginate(10);

        $responseData = [
            'current_page' => $logs->currentPage(),
            'entries' => new LogCollection($logs->items()),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
        ];

        return $this->successResponse("Logs has been successfully found.", $responseData);
    }

    /**
     * Update an existing log.
     * This method is accessible only to administrators.
     *
     * @param int $id
     * @param  \App\Http\Requests\LogRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, LogRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $log = Log::find($id);

        if (!$log) return $this->errorResponse("Log not found.", 404);

        if (!$log->update($request->validated())) return $this->errorResponse("An error occurred while updating the log entry, try again later.", 500);

        return $this->successResponse("Log has been successfully updated.", new LogResource($log));
    }

}
