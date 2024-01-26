<?php

namespace App\Http\Controllers;

use App\Models\Hello;
use App\Http\Requests\StoreHelloRequest;
use App\Http\Requests\UpdateHelloRequest;

class HelloController extends Controller
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
    public function store(StoreHelloRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hello $hello)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hello $hello)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHelloRequest $request, Hello $hello)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hello $hello)
    {
        //
    }
}
