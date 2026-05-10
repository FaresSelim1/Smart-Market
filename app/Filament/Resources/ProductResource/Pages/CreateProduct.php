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
        ProductResource::saveProductImages($this->record, $data);
    }
}
