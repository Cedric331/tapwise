<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $contactEmail = config('services.contact_email');
        if (! $contactEmail) {
            return back()->withErrors([
                'form' => "L'adresse email de contact n'est pas configurée.",
            ]);
        }

        Mail::to($contactEmail)->send(new ContactMessageMail($data));

        return back()->with('success', 'Message envoyé. Nous vous répondrons rapidement.');
    }
}

