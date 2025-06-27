<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Error;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Support\Collection;

trait HasGeneralFieldForm
{
    public function getPossibleTypeOptions(?GeneralField $record): array|Collection
    {
        if (is_null($record)) {
            return [];
        }

        $type = $record->getType();

        return collect($type->getFlattenExtraTypeOptions())
            //Remove any where can be use
            ->filter(fn(TypeOption $typeOption) => $typeOption->canBeOverwrittenByNonField())
            ->mapWithKeys(function (TypeOption $value, string $key) {
                try {
                    $label = $value->getModifyOptionComponent($key)->getLabel() ?? $key;

                    return [$key => $label];
                } catch (Error) {
                    //When label need record or livewire component
                    return [$key => $key];
                }
            });
        //ToDo may with filtering (Some are not good to select in GeneralField)
    }

    public function getOverwrittenOptionDynamicSchema(): array
    {
        $record = $this->getRecord();

        if (is_null($record)) {
            return [];
        }

        $type = $record->getType();
        $components = $type->getExtraTypeOptionComponents();
        $isOverwritten = function ($component, $get) {
            $key = $component->getStatePath(false);
            $values = $get('../overwrite_option_keys') ?? [];

            return in_array($key, $values, false);
        };

        foreach ($components as $item) {
            if ($item instanceof Field) {
                $item->visible($isOverwritten);
            } elseif ($item instanceof Section) {
                foreach ($item->getChildComponents() as $field) {
                    $field->visible($isOverwritten);
                }
            }
        }

        return $components;
    }

    protected function getGeneralFieldBasicSettings(): Section
    {
        return Section::make()
            ->columnSpan(2)
            ->columns()
            ->schema([
                TextInput::make('name')
                    ->label($this->label(...))
                    ->helperText($this->helperText(...))
                    ->columnStart(1)
                    ->columnSpan(1),
                Group::make([
                    Select::make('type')
                        ->options($this->getAllCustomFieldTypeOptions())
                        ->helperText($this->helperText(...))
                        ->disabledOn('edit')
                        ->label($this->label(...))
                        ->afterStateUpdated(function ($set) {
                            $set('overwrite_option_keys', []);
                            $set('overwrite_options', []);
                        })
                        ->columnStart(1)
                        ->columnSpan(1)
                        ->required()
                        ->live(),
                    IconPicker::make('icon')
                        ->required()
                        ->helperText($this->helperText(...))
                        ->label($this->label(...)),
                ]),
                TextInput::make('identifier')
                    ->helperText($this->helperText(...))
                    ->label($this->label(...))
                    ->disabled(fn($livewire) => $livewire instanceof EditRecord)
                    ->columnSpan(1)
                    ->required(),
                Toggle::make('is_active')
                    ->helperText($this->helperText(...))
                    ->label($this->label(...))
                    ->default(true)
                    ->columnSpan(1),
            ]);
    }

    protected function helperText(Component $component): string
    {
        return GeneralField::__('attributes.' . $component->getStatePath(false) . '.helper_text');
    }

    protected function label(Component $component): string
    {
        return GeneralField::__('attributes.' . $component->getStatePath(false) . '.label');
    }

    protected function getAllCustomFieldTypeOptions(): Collection
    {
        $types = CustomFieldType::getSelectableGeneralFieldTypes();

        return collect($types)->map(fn($type) => ($type)::make()->getTranslatedName());
    }

    protected function getOverwriteTypeOptions(): Component
    {
        return Fieldset::make(GeneralField::__('attributes.overwrite_options.label'))
            ->columnSpan(1)
            ->columns(1)
            ->hidden($this->hasFieldTypeOptions(...))
            ->schema([
                Placeholder::make('message')
                    ->label(GeneralField::__('attributes.overwrite_options.message_on_create'))
                    ->hiddenOn('edit'),
                Select::make('overwrite_option_keys')
                    ->options($this->getPossibleTypeOptions($this->getRecord()))
                    ->label('')
                    ->hiddenOn('create')
                    ->columnStart(1)
                    ->columnSpan(1)
                    ->required()
                    ->multiple()
                    ->live(),
                Group::make()
                    ->hiddenOn('create')
                    ->statePath('overwrite_options')
                    ->schema($this->getOverwrittenOptionDynamicSchema()),
            ]);
    }

    protected function hasFieldTypeOptions(?GeneralField $record): bool
    {
        if (is_null($record)) {
            return false;
        }

        $type = $record->getType();
        $array = $type->getFlattenExtraTypeOptions();

        return empty($array);
    }

    protected function getGeneralFieldTypeOptions(): Fieldset
    {
        /**@var CustomFieldType|null $type */
        $type = $this
            ->getRecord()
            ?->getType();
        $schema = [];
        $visable = true;

        if (!is_null($type)) {
            $schema = $type->getGeneralTypeOptionComponents();
            $visable = count($type->generalTypeOptions()) > 0;
        }

        return Fieldset::make(GeneralField::__('attributes.options.label'))
            ->columnSpan(1)
            ->columns(1)
            ->statePath('options')
            ->visible($visable)
            ->schema([
                Placeholder::make('message')
                    ->label(GeneralField::__('attributes.options.message_on_create'))
                    ->hiddenOn('edit'),
                Group::make()
                    ->hiddenOn('create')
                    ->schema($schema),
            ]);
    }
}
