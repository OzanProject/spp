<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sort = $request->get('sort', 'latest');

        $query = Student::with(['user', 'classRoom']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort == 'az') {
            $query->join('users', 'users.id', '=', 'students.user_id')
                  ->orderBy('users.name', 'asc')
                  ->select('students.*');
        } elseif ($sort == 'za') {
            $query->join('users', 'users.id', '=', 'students.user_id')
                  ->orderBy('users.name', 'desc')
                  ->select('students.*');
        } else {
            $query->latest();
        }

        $students = $query->paginate($perPage)->withQueryString();
        return view('backend.students.index', compact('students'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        return view('backend.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nis' => 'required|unique:students',
            'class_room_id' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student'
        ]);

        Student::create([
            'user_id' => $user->id,
            'class_room_id' => $request->class_room_id,
            'nis' => $request->nis,
            'parent_phone' => $request->parent_phone
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Data Siswa berhasil ditambahkan');
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::all();
        return view('backend.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$student->user_id,
            'nis' => 'required|unique:students,nis,'.$student->id,
            'class_room_id' => 'required',
        ]);

        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $student->user->update(['password' => Hash::make($request->password)]);
        }

        $student->update([
            'class_room_id' => $request->class_room_id,
            'nis' => $request->nis,
            'parent_phone' => $request->parent_phone
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Data Siswa berhasil diupdate');
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        return redirect()->route('admin.students.index')->with('success', 'Data Siswa berhasil dihapus');
    }

    public function export()
    {
        $filename = 'Data_Siswa_' . date('Ymd_Hi') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentsExport, $filename);
    }

    public function template()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentsTemplateExport, 'Template_Import_Siswa_SPP.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport, $request->file('file'));

        return back()->with('success', 'Data siswa berhasil diimport.');
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data untuk dihapus.');
        }

        $students = Student::whereIn('id', $ids)->get();
        foreach ($students as $student) {
            $student->user->delete(); // Student also deleted via cascade or manual if no cascade
        }

        return redirect()->route('admin.students.index')->with('success', count($ids) . ' data siswa berhasil dihapus.');
    }
}
