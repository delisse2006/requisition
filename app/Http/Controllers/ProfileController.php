<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
            $this->handleAvatarUpload($user, $request->file('avatar'));
        }

        $user->save();
        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }

    private function handleAvatarUpload($user, $file)
    {
        // Delete old avatar if exists
        if ($user->avatar) {
            $oldPath = public_path('images/avatars/' . $user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Save new avatar
        $extension = $file->getClientOriginalExtension();
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
        $file->move(public_path('images/avatars'), $filename);
        $user->avatar = $filename;
    }
}