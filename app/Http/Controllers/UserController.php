<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('subscriptions')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }
}