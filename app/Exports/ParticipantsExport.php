<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ParticipantsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Participant::where('event_id', $this->eventId)
            ->select('name', 'email', 'phone', 'company', 'position', 'city')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Email',
            'Telefone',
            'Empresa',
            'Cargo',
            'Cidade',
        ];
    }
}
