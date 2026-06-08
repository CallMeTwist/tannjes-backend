<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('department.name')->label('Department')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'gray' => 'pending_payment',
                    'warning' => 'pending_approval',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending_payment' => 'Pending payment',
                    'pending_approval' => 'Pending approval',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('viewProof')
                    ->label('Proof')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(fn (Patient $r) => $r->payments()->latest()->value('proof_path') !== null)
                    ->url(fn (Patient $r) => Storage::disk('public')->url($r->payments()->latest()->value('proof_path')))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Patient $r) => $r->status === Patient::STATUS_PENDING_APPROVAL)
                    ->action(function (Patient $r) {
                        \App\Services\PatientApproval::approve($r, auth()->id());
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Patient $r) => in_array($r->status, [Patient::STATUS_PENDING_APPROVAL, Patient::STATUS_PENDING_PAYMENT]))
                    ->action(fn (Patient $r) => $r->update(['status' => Patient::STATUS_REJECTED])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
        ];
    }
}
