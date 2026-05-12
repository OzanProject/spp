<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::withCount('students')->latest()->get();
        return view('backend.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('backend.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:class_rooms,name',
        ], [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        ClassRoom::create(['name' => $request->name]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(ClassRoom $class)
    {
        return view('backend.classes.edit', compact('class'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:class_rooms,name,' . $class->id,
        ], [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        $class->update(['name' => $request->name]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassRoom $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Kelas tidak bisa dihapus karena masih ada siswa terdaftar.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
