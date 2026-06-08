<?php
namespace App\Filament\Resources\ConsultationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';
    protected static ?string $title = 'Conversation';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')->required()->rows(4)->label('Reply'),
            Forms\Components\FileUpload::make('attachment_path')
                ->disk('public')->directory('consultation-attachments')
                ->label('Attachment')->acceptedFileTypes(['image/*', 'application/pdf']),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->defaultSort('created_at')
            ->columns([
                Tables\Columns\TextColumn::make('sender_type')->badge()->colors([
                    'success' => 'staff',
                    'gray' => 'patient',
                ]),
                Tables\Columns\TextColumn::make('body')->wrap()->limit(120),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Reply')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sender_type'] = 'staff';
                        $data['sender_id'] = auth()->id();
                        return $data;
                    })
                    ->after(fn ($record) => $record->consultation->update(['last_message_at' => now()])),
            ])
            ->actions([]);
    }
}
