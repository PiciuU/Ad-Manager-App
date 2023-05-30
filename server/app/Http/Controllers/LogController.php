<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Http\Requests\LogRequest;
use App\Http\Resources\LogResource;
use App\Http\Resources\LogCollection;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Log $logs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Log $logs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LogRequest $request, Log $logs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Log $logs)
    {
        //
    }
}
