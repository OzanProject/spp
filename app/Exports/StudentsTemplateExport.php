<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Berikan 2 contoh data palsu (dummy) agar user paham formatnya
        return [
            [
                '100123',
                'Budi Santoso',
                'budi@siswa.test',
                '1',
                '081234567890'
            ],
            [
                '100124',
                'Siti Aminah',
                'siti@siswa.test',
                '1',
                '081987654321'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nis',
            'nama',
            'email',
            'id_kelas',
            'no_hp_ortu'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF435EBE']]],
        ];
    }
}
