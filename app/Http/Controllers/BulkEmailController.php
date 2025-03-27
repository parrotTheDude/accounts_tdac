<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BulkEmail;


class BulkEmailController extends Controller
{
    public function index()
    {
        $emails = BulkEmail::with('user')->latest()->paginate(20);
        return view('emails.history', compact('emails'));
    }
}
