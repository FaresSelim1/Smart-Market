<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Load existing branch stock into the repeater when editing.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load branch stock
        $data['branch_stock'] = $this->record->branches->map(function ($branch) {
            return [
                'branch_id'            => $branch->id,
                'stock_level'          => $branch->pivot->stock_level,
                'low_stock_threshold'  => $branch->pivot->low_stock_threshold,
            ];
        })->toArray();

        return $data;
    }

    /**
     * After saving, sync branch stock and save new images.
     */
    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        ProductResource::syncBranchStock($this->record, $data);
    }
}
