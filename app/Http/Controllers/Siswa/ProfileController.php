<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $student = $user->student;
        return view('siswa.profile.edit', compact('user', 'student'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_parent' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                Storage::disk('public')->delete('photos/' . $user->photo);
            }
            $filename = 'student_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->storeAs('photos', $filename, 'public');
            $user->photo = $filename;
        }

        $user->save();

        $student->parent_phone = $request->phone_parent;
        $student->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
