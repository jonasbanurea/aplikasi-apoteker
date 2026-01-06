<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Supplier::orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Supplier',
            'Kontak Person',
            'Telepon',
            'Email',
            'Alamat',
            'Kota',
            'Provinsi',
            'Kode Pos',
            'NPWP',
            'Status Aktif',
            'Payment Terms (hari)',
            'Catatan',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->kode,
            $supplier->nama,
            $supplier->kontak_person ?? '-',
            $supplier->telepon ?? '-',
            $supplier->email ?? '-',
            $supplier->alamat ?? '-',
            $supplier->kota ?? '-',
            $supplier->provinsi ?? '-',
            $supplier->kode_pos ?? '-',
            $supplier->npwp ?? '-',
            $supplier->is_active ? 'Aktif' : 'Tidak Aktif',
            $supplier->payment_terms ?? 0,
            $supplier->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // Kode
            'B' => 30, // Nama
            'C' => 20, // Kontak
            'D' => 15, // Telepon
            'E' => 25, // Email
            'F' => 40, // Alamat
            'G' => 20, // Kota
            'H' => 20, // Provinsi
            'I' => 12, // Kode Pos
            'J' => 20, // NPWP
            'K' => 12, // Status
            'L' => 15, // Payment Terms
            'M' => 30, // Catatan
        ];
    }
}
