<?php

namespace App\Filament\Widgets;

use App\Models\CalendarEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingEventsWidget extends TableWidget
{
    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Scadenze e Attività')
            ->query(fn (): Builder => CalendarEvent::query()
                ->where('is_completed', false)
                ->where(fn ($q) => $q
                    ->where('starts_at', '<', now())
                    ->orWhere(fn ($q2) => $q2
                        ->where('starts_at', '>=', now())
                        ->where('starts_at', '<=', now()->addDays(14))
                    )
                )
                ->orderBy('starts_at')
            )
            ->columns([
                TextColumn::make('status_badge')
                    ->label('Stato')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->starts_at->isPast() ? 'Scaduta' : 'In arrivo')
                    ->color(fn ($record) => $record->starts_at->isPast() ? 'danger' : 'success'),

                TextColumn::make('title')
                    ->label('Evento')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->starts_at->diffForHumans()),

                TextColumn::make('starts_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->emptyStateHeading('Nessuna scadenza nei prossimi 14 giorni')
            ->emptyStateIcon('heroicon-o-calendar')
            ->paginated(false);
    }
}
