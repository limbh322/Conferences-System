<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conference;

class AuthorController extends Controller
{
    public function home()
    {
        $conferences = Conference::all(); // get all conferences
        return view('home', compact('conferences'));
    }
}
