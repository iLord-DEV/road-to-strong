<?php

namespace App\Http\Controllers;

use App\Models\FtpEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FtpController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'watts' => ['required', 'integer', 'min:50', 'max:600'],
            'tested_at' => ['required', 'date', 'before_or_equal:today'],
        ]);

        FtpEntry::create([
            'user_id' => $request->user()->id,
            'watts' => $validated['watts'],
            'tested_at' => $validated['tested_at'],
        ]);

        return redirect()->route('history');
    }

    public function destroy(Request $request, FtpEntry $ftpEntry): RedirectResponse
    {
        abort_unless($ftpEntry->user_id === $request->user()->id, 403);

        $ftpEntry->delete();

        return redirect()->route('history');
    }
}
