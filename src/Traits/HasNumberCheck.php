<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Support\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

trait HasNumberCheck
{
    protected function getEqualBiggerSmallerAction($greater): Action
    {
        $prefix = $greater ? 'greater' : 'smaller';
        $attribute = $prefix . '_equals';
        $icon1 = $greater ? 'tabler-math-equal-lower' : 'tabler-math-equal-greater';
        $icon2 = $greater ? 'tabler-math-lower' : 'tabler-math-greater';

        return Action::make($attribute . '_action')
            ->action(fn($set, $get) => $set($attribute, !($get($attribute) ?? false)))
            ->color(Color::hex('#000000'))
            ->icon(fn($get) => $get($attribute) ? $icon1 : $icon2);
    }

    protected function checkNumber(mixed $targetValue, array $data): bool
    {
        $value = (float)$targetValue;

        if ($data['exactly_number']) {
            return $data['number'] === $value;
        }

        if (!empty($data['greater_than'])) {
            $this->checkGreaterThan($value, $data);
        }

        if (!empty($data['smaller_than'])) {
            $this->checkSmallerThan($value, $data);
        }

        return true;
    }

    protected function checkSmallerThan(float $value, array $data): bool
    {
        $inclusive = $data['smaller_equals'];
        $threshold = $data['smaller_than'];

        return $inclusive ? $value <= $threshold : $value < $threshold;
    }

    protected function checkGreaterThan(float $value, array $data): bool
    {
        $threshold = $data['greater_than'];
        $inclusive = $data['greater_equals'];

        return $inclusive ? $value >= $threshold : $value > $threshold;
    }

    protected function getNumberTypeGroup(): Component
    {
        return Group::make([
            Checkbox::make('exactly_number')
                ->label(static::__('number.exactly_number'))
                ->columnSpanFull()
                ->live(),
            TextInput::make('number')
                ->label(static::__('number.number'))
                ->prefixIcon('carbon-character-whole-number')
                ->visible(fn($get) => $get('exactly_number'))
                ->required()
                ->numeric(),
            Group::make()
                ->hidden(fn($get) => $get('exactly_number'))
                ->columns(5)
                ->columnSpanFull()
                ->schema([
                    Hidden::make('greater_equals'),
                    Hidden::make('smaller_equals'),
                    TextInput::make('greater_than')
                        ->label(static::__('number.greater_than'))
                        ->suffixAction($this->getEqualBiggerSmallerAction(true))
                        ->columnStart(1)
                        ->columnSpan(2)
                        ->numeric(),
                    Placeholder::make('')
                        ->content(fn() => new HtmlString(
                            Blade::render(
                                '<div class="flex flex-col items-center justify-center"><br><x-bi-input-cursor style="height: auto; width: 40px"/></div>'
                            )
                        ))
                        ->label(' '),
                    TextInput::make('smaller_than')
                        ->label(static::__('number.smaller_than'))
                        ->prefixAction($this->getEqualBiggerSmallerAction(false))
                        ->columnStart(4)
                        ->columnSpan(2)
                        ->numeric(),
                ]),
            Placeholder::make('')
                ->content(fn() => static::__('number.greater_smaller_info_on_empty'))
                ->columnSpanFull()
                ->label(''),
        ]);
    }
}
