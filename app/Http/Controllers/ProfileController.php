<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function show(): View
{
    $user = auth()->user();
    
    // Load teacher data
    if ($user->role === 'teacher' && $user->teacher) {
        $user->teacher->load('classes.grade', 'classes.academicYear');
    }

    // Use QR login URL instead of profile URL
    $qrData = $user->qrLoginUrl();

    return view('profile.show', compact('user', 'qrData'));
}

    /**
     * Show edit form
     */
    public function edit(): View
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update basic info
     */
    public function update(Request $request): RedirectResponse
{
    $user = auth()->user();

    $rules = [
        'name'          => ['required', 'string', 'max:255'],
        'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'phone'         => ['nullable', 'string', 'max:20'],
        'date_of_birth' => ['nullable', 'date', 'before:today'],
        'gender'        => ['nullable', 'in:male,female'],
        'nationality'   => ['nullable', 'string', 'max:100'],
        'ethnicity'     => ['nullable', 'string', 'max:100'],
    ];

    // Address fields — admin only
    if ($user->isAdmin()) {
        $rules['birth_place']     = ['nullable', 'string', 'max:255'];
        $rules['current_address'] = ['nullable', 'string', 'max:500'];
    }

    $validated = $request->validate($rules);

    $user->update($validated);

    return redirect()->route('profile.show')
        ->with('success', 'ព័ត៌មានផ្ទាល់ខ្លួនត្រូវបានធ្វើបច្ចុប្បន្នភាព។');
}

    /**
     * Update password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'ពាក្យសម្ងាត់ត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ។');
    }

    /**
     * Upload avatar
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return redirect()->route('profile.show')
            ->with('success', 'រូបភាពត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ។');
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return redirect()->route('profile.show')
            ->with('success', 'រូបភាពត្រូវបានលុប។');
    }
}