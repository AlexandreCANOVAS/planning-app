<?php

namespace App\Exports;

use App\Models\Lieu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LieuxExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $societeId;

    public function __construct($societeId)
    {
        $this->societeId = $societeId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Lieu::where('societe_id', $this->societeId)
            ->orderBy('nom')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Adresse',
            'Ville',
            'Code postal',
            'Téléphone',
            'Contact principal',
            'Horaires',
            'Latitude',
            'Longitude',
        ];
    }

    /**
     * @param mixed $lieu
     * @return array
     */
    public function map($lieu): array
    {
        return [
            $lieu->id,
            $lieu->nom,
            $lieu->adresse,
            $lieu->ville,
            $lieu->code_postal,
            $lieu->telephone,
            $lieu->contact_principal,
            $lieu->horaires,
            $lieu->latitude,
            $lieu->longitude,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
}
