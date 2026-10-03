<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        // Role tidak pernah diambil dari request.
        $request->user()->update($request->validated());

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(PasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => Hash::make($request->validated()['password'])]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
