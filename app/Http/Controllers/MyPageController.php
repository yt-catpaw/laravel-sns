<?php

namespace App\Http\Controllers;

class MyPageController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('mypage.index', compact('user'));
    }
}
