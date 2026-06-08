<?php
namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewConsultation extends ViewRecord
{
    protected static string $resource = ConsultationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('patient.name')->label('Patient'),
            TextEntry::make('subject'),
            TextEntry::make('doctor.name')->label('Doctor')->placeholder('Unassigned'),
            TextEntry::make('status')->badge(),
        ]);
    }
}
