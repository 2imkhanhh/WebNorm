<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();

        return Inertia::render('Contacts', [
            'contacts' => $contacts,
        ]);
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,completed',
            'notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return back()->with('success', 'Đã cập nhật trạng thái liên hệ!');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return back()->with('success', 'Đã xóa liên hệ!');
    }
}

