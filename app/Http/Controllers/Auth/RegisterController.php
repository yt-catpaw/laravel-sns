<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function show()
    {
        return view('register.index');
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        // TODO: implement registration logic
        return back();
    }
}
