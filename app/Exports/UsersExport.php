<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = User::query()->with(['roles', 'defaultShift'])->whereDoesntHave('roles', function($q) {
            $q->where('name', 'Admin');
        });

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('jabatan')) {
            $query->where('jabatan', $this->request->jabatan);
        }

        if ($this->request->filled('role')) {
            $query->role($this->request->role);
        }
        
        $query->latest();

        return $query;
    }

    public function headings(): array
    {
        return [
            'UID',
            'Nama',
            'Email',
            'Jabatan',
            'Role',
            'Default Shift',
            'Tgl Bergabung'
        ];
    }

    public function map($user): array
    {
        return [
            $user->uid ?? '-',
            $user->name,
            $user->email,
            $user->jabatan ?? '-',
            $user->roles->pluck('name')->implode(', ') ?: '-',
            $user->defaultShift ? $user->defaultShift->nama : '-',
            $user->created_at ? $user->created_at->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981'] // Emerald green
                ]
            ],
        ];
    }
}
