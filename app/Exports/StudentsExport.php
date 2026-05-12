<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Student::with(['user', 'classRoom'])->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Lengkap',
            'Email',
            'Nama Kelas',
            'No. HP Orang Tua'
        ];
    }

    public function map($student): array
    {
        return [
            $student->nis,
            $student->user->name ?? '-',
            $student->user->email ?? '-',
            $student->classRoom->name ?? '-',
            $student->parent_phone ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF435EBE']]],
        ];
    }
}
