<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    /**
     * Send contact form email
     */
    public function sendContactEmail(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data = [
    'name'         => $validated['name'],
    'email'        => $validated['email'],
    'subject'      => $validated['subject'],
    'user_message' => $validated['message'], // renamed
];
        $recipientEmail = 'rodolph.khoury@net.usj.edu.lb';

        try {
            // Send email to admin
            Mail::send('emails.contact', $data, function ($m) use ($recipientEmail, $validated) {
    $m->to($recipientEmail)
      ->subject('New Contact Form Submission: ' . $validated['subject'])
      ->replyTo($validated['email'], $validated['name']);
});
            // Redirect back with success flash message
            info('sendt');
            return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Contact form email failed: '.$e->getMessage());

            // Redirect back with error flash message
            return redirect()->back()->with('error', 'Failed to send email. Please try again later.');
        }
    }
}
