<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Unique coupon code customers will enter'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'fixed'   => 'Fixed Amount ($)',
                                'percent' => 'Percentage (%)',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->required()
                            ->helperText('Amount or percentage to discount'),

                        Forms\Components\TextInput::make('min_cart_value')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->helperText('Minimum cart value to apply this coupon'),

                        Forms\Components\TextInput::make('max_uses')
                            ->numeric()
                            ->nullable()
                            ->helperText('Leave empty for unlimited uses'),

                        Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->nullable()
                            ->helperText('Leave empty for no expiry'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => $state === 'percent' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percent'
                        ? $record->value . '%'
                        : '$' . number_format($record->value, 2)),

                Tables\Columns\TextColumn::make('min_cart_value')
                    ->money('usd')
                    ->label('Min Cart'),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($record) => $record->used_count . ' / ' . ($record->max_uses ?? '∞')),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('M d, Y')
                    ->placeholder('Never'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
