<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PostmarkService;

class EmailVerificationController extends Controller
{
    public function send(Request $request, PostmarkService $postmark)
    {
        $user = $request->user();

        // Delete any existing token for this user
        VerificationToken::where('user_id', $user->id)->delete();

        // Generate a secure token
        $token = Str::upper(Str::random(6));

        // Save to DB
        VerificationToken::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addHour(),
        ]);

        // Build the URL (adjust path as needed)
        $url = url("/verify-email?token={$token}&email=" . urlencode($user->email));

        // Send via Postmark
        $postmark->sendVerificationEmail($user->email, $url);

        return back()->with('success', 'Verification email sent to ' . $user->email);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $token = VerificationToken::where('user_id', $user->id)
        ->where('token', strtoupper($request->token)) // use $request->token instead of code
        ->where('expires_at', '>', now())
        ->first();

        if (!$token) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->markEmailAsVerified(); // Custom method or Laravel built-in if supported
        $token->delete();

        return redirect()->route('dashboard')->with('success', 'Email verified!');
    }
}
