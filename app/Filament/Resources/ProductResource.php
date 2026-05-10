<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use Filament\Forms\Components\FileUpload;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Details')
                    ->description('Basic product information.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->unique(ignoreRecord: true)
                            ->required(),

                        FileUpload::make('product_images')
                            ->label('Product Images')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(5120)
                            ->helperText('Upload multiple images for this product.')
                            ->dehydrated(false),

                    ])->columns(2),

                Forms\Components\Section::make('Inventory Management')
                    ->description('Set stock levels for different branches.')
                    ->schema([
                        Forms\Components\Repeater::make('branch_stock')
                            ->label('Branch stock assignments')
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->label('Branch')
                                    ->options(Branch::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\TextInput::make('stock_level')
                                    ->label('Current Stock')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                                Forms\Components\TextInput::make('low_stock_threshold')
                                    ->label('Alert Threshold')
                                    ->numeric()
                                    ->default(10)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->grid(1)
                            ->itemLabel(fn (array $state): ?string => Branch::find($state['branch_id'] ?? null)?->name ?? 'Branch')
                            ->addActionLabel('Add Branch Stock')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('price')->money('usd')->sortable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->copyable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return Excel::download(
                            new ProductsExport(),
                            'products.xlsx'
                        );
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('Excel File')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $path = storage_path('app/public/' . $data['file']);
                        Excel::import(new \App\Imports\ProductsImport(), $path);
                        \Filament\Notifications\Notification::make()
                            ->title('Products imported successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Sync branch stock from the repeater form data.
     */
    public static function syncBranchStock(Product $record, array $data): void
    {
        $branchStock = $data['branch_stock'] ?? [];
        if (! is_array($branchStock) || empty($branchStock)) {
            return;
        }

        $syncData = collect($branchStock)
            ->filter(fn ($row) => ! empty($row['branch_id']))
            ->mapWithKeys(function ($row) {
                $branchId = (int) $row['branch_id'];
                return [
                    $branchId => [
                        'stock_level'          => (int) ($row['stock_level'] ?? 0),
                        'low_stock_threshold'  => (int) ($row['low_stock_threshold'] ?? 10),
                    ],
                ];
            })
            ->all();

        if (! empty($syncData)) {
            $record->branches()->sync($syncData);
        }
    }

    /**
     * Save uploaded images as ProductImage records.
     */
    public static function saveProductImages(Product $record, array $data): void
    {
        $images = $data['product_images'] ?? [];
        if (! is_array($images) || empty($images)) {
            return;
        }

        // Normalize array keys to ensure $index is always a clean integer
        // This avoids UUID string keys from Livewire's internal state
        $normalizedImages = array_values(array_filter($images, fn($path) => is_string($path)));

        foreach ($normalizedImages as $index => $path) {
            $record->images()->create([
                'path'       => $path,
                'sort_order' => (int) $index,
            ]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}