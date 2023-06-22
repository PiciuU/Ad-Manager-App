<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Log;
use App\Http\Requests\LogRequest;
use App\Http\Resources\LogResource;
use App\Http\Resources\LogCollection;

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
        'CREATE_AD' => 'Utworzono nową reklamę',
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

        // Zwróć odpowiedź z sukcesem
        return true;
    }

    /**
     * Display a listing of the all logs.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $logs = Log::orderBy('created_at', 'desc')->paginate(15);

        $responseData = [
            'current_page' => $logs->currentPage(),
            'entries' => new LogCollection($logs->items()),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
        ];

        return $this->successResponse('Logs found', $responseData);
    }

    /**
     * Display the logs for specified user account
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $user = User::find($id);

        if (!$user) return $this->errorResponse('User not found', 404);

        $logs = $user->logs()->orderBy('created_at', 'desc')->paginate(10);

        $responseData = [
            'current_page' => $logs->currentPage(),
            'entries' => new LogCollection($logs->items()),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
        ];

        return $this->successResponse('Logs found', $responseData);
    }

    /**
     * Update the specified log in storage.
     *
     * @param int $id
     * @param  \App\Http\Requests\LogRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, LogRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $log = Log::find($id);

        if (!$log) return $this->errorResponse('Log not found', 404);

        if (!$log->update($request->validated())) return $this->errorResponse('An error occurred while updating the log entry, try again later', 500);

        return $this->successResponse('Log entry has been successfully updated', new LogResource($log));
    }

}
