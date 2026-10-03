<?php

namespace App\Http\Controllers;

use App\Models\Brief;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/** Preview & download PDF. Path storage tidak pernah diekspos ke browser. */
class BriefFileController extends Controller
{
    private function authorizeAccess(Request $request, Brief $brief): void
    {
        $user = $request->user();

        $allowed = match ($user->role) {
            'admin', 'marketing' => true,
            'user' => $brief->user_id === $user->id,
            'creative' => $brief->status === Brief::STATUS_APPROVED,
            default => false,
        };

        abort_unless($allowed, 403, 'Anda tidak memiliki akses ke file ini.');
        abort_unless(Storage::disk('local')->exists($brief->file_path), 404, 'File brief tidak ditemukan.');
    }

    public function preview(Request $request, Brief $brief): Response
    {
        $this->authorizeAccess($request, $brief);

        return Storage::disk('local')->response(
            $brief->file_path,
            $brief->original_filename,
            ['Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }

    public function download(Request $request, Brief $brief): Response
    {
        $this->authorizeAccess($request, $brief);

        return Storage::disk('local')->download(
            $brief->file_path,
            $brief->original_filename,
            ['Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff']
        );
    }
}
