<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cấu hình giảm giá')
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('type')
                            ->options([
                                'percentage' => 'Phần trăm (%)',
                                'fixed_amount' => 'Số tiền cố định (VNĐ)',
                                'free_shipping' => 'Miễn phí vận chuyển',
                            ])
                            ->required(),
                        TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->helperText('Số tiền hoặc % được giảm'),
                        TextInput::make('min_order_amount')
                            ->required()
                            ->numeric()
                            ->prefix('VNĐ')
                            ->default(0),
                        TextInput::make('max_discount')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->helperText('Giới hạn giảm tối đa (với loại %)'),
                    ])->columns(2),

                Section::make('Đối tượng áp dụng')
                    ->schema([
                        Select::make('applies_to')
                            ->options([
                                'all' => 'Toàn bộ cửa hàng',
                                'products' => 'Sản phẩm cụ thể',
                                'services' => 'Dịch vụ cụ thể',
                                'specific' => 'ID cụ thể',
                            ])
                            ->default('all')
                            ->required(),
                        TagsInput::make('applicable_ids')
                            ->placeholder('Nhập ID sản phẩm/dịch vụ...')
                            ->helperText('Để trống nếu áp dụng cho tất cả'),
                    ]),

                Section::make('Hạn mức & Thời gian')
                    ->schema([
                        TextInput::make('usage_limit')
                            ->numeric()
                            ->placeholder('Không giới hạn'),
                        TextInput::make('usage_per_user')
                            ->required()
                            ->numeric()
                            ->default(1),
                        DateTimePicker::make('starts_at')
                            ->required()
                            ->default(now()),
                        DateTimePicker::make('expires_at')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
