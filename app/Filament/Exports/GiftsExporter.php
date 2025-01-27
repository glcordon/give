<?php

namespace App\Filament\Exports;

use App\Models\Gifts;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class GiftsExporter extends Exporter
{
    protected static ?string $model = Gifts::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('campaign.name'),
            ExportColumn::make('amount'),
            ExportColumn::make('member.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your gifts export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
