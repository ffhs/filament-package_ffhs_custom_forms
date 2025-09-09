<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropAction;
use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FfhsUtils\Filament\DragDrop\DragDropSelectAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('short_title')
                ->label(CustomForm::__('attributes.short_title'))
                ->required(),
            CustomFormTypeSelector::make()
                ->required(),
            Group::make([
                DragDropAction::make('test_action')
                    ->action(function ($path, $position, $get, $set) {
                        $data = $get($path, true);
                        if (is_null($data)) {
                            $data = [];
                        }
                        $toAdd = [
                            'name' => 'new'
                        ];

                        // Split the array into two parts
                        $before = array_slice($data, 0, $position, true);
                        $after = array_slice($data, $position, null, true);

// Merge with the new element in between
                        $arr = $before + [uniqid() => $toAdd] + $after;

                        $set($path, $arr, true);
                    }),

                DragDropSelectAction::make('test_action_select')
                    ->options([
                        'test' => 'Test',
                        'test2' => 'Test2',
                        'test3' => 'Test3'
                    ])
                    ->disableOptionWhen(function ($value) {
                        return $value === 'test2';
                    })
                    ->expandSelect()
                    ->optionIcons([
                        'test' => Heroicon::ClipboardDocument
                    ])
                    ->action(function ($arguments) {
                        dd($arguments);
                    })
            ]),
            DragDropGroup::make('test')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name'),
                ])
        ]);
    }
}
