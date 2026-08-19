<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['101', 'Budi Santoso', 'budi@example.com', 'password123', 'Guru', 'Karyawan'],
            ['102', 'Siti Aminah', 'siti@example.com', 'password123', 'Staff Tata Usaha', 'Karyawan'],
        ];
    }

    public function headings(): array
    {
        return [
            'uid',
            'name',
            'email',
            'password',
            'jabatan',
            'role'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with a background color.
            1    => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Indigo color
                ]
            ],
        ];
    }
}
