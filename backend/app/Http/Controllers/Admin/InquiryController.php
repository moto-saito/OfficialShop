<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        $statuses = Inquiry::STATUSES;

        return view('admin.inquiries.show', compact('inquiry', 'statuses'));
    }

    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(Inquiry::STATUSES)),
        ], [
            'status.required' => 'ステータスを選択してください。',
            'status.in' => 'ステータスが正しくありません。',
        ]);

        $inquiry->update($validated);

        return redirect()->route('admin.inquiries.show', $inquiry)->with('success', 'ステータスを更新しました。');
    }
}
