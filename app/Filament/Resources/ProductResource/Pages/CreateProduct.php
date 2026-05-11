<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * After creating the product, sync branch stock and save images.
     */
    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        ProductResource::syncBranchStock($this->record, $data);
        
        // Refresh the form state with permanent paths to clear temporary upload state
        $this->data['product_images'] = $this->record->images()
                ->orderBy('sort_order')
                ->pluck('path')
                ->toArray();
    }
}
