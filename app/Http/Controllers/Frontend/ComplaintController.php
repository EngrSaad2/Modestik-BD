<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('frontend.complaints');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_number' => 'nullable|string|max:50',
            'phone' => 'required|string|regex:/^01[3-9]\d{8}$/',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'name.required' => 'অনুগ্রহ করে আপনার নাম লিখুন।',
            'phone.required' => 'অনুগ্রহ করে আপনার মোবাইল নম্বর লিখুন।',
            'phone.regex' => 'অনুগ্রহ করে একটি সঠিক ১১ ডিজিটের মোবাইল নম্বর লিখুন।',
            'subject.required' => 'অভিযোগের বিষয়বস্তু লিখুন।',
            'message.required' => 'আপনার অভিযোগের বিবরণ লিখুন।',
        ]);

        $complaint = Complaint::create([
            'name' => $request->name,
            'order_number' => $request->order_number,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Send Email Notification to Admin immediately
        try {
            Mail::raw(
                "নতুন অভিযোগ জমা পড়েছে:\n\n" .
                "নাম: {$complaint->name}\n" .
                "অর্ডার নম্বর: {$complaint->order_number}\n" .
                "মোবাইল নম্বর: {$complaint->phone}\n" .
                "বিষয়: {$complaint->subject}\n\n" .
                "অভিযোগের বিবরণ:\n{$complaint->message}",
                function ($message) use ($complaint) {
                    $message->to('admin@triangletech.com.bd')
                        ->subject('নতুন অভিযোগ জমা পড়েছে: ' . $complaint->subject);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Complaint Admin Mail failed: ' . $e->getMessage());
        }

        return back()->with('success', 'আপনার অভিযোগটি সফলভাবে জমা নেওয়া হয়েছে। আমাদের টিম এটি পর্যালোচনা করে দ্রুত যোগাযোগ করবে।');
    }
}
