<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Schemas;

use Error;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\Collection;

class GeneralFieldSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            static::getGeneralFieldSection(),
            Group::make(static fn($record) => is_null($record) ? [] : [
                static::getGeneralFieldTypeOptions($record),
                static::getOverwriteTypeOptions($record)
            ])
                ->columnSpanFull()
                ->columns(2),
        ]);
    }

    public static function getOverwrittenOptionDynamicSchema(?GeneralField $record): array
    {
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
                foreach ($item->getDefaultChildComponents() as $field) {
                    $field->visible($isOverwritten);
                }
            }
        }

        return $components;
    }

    protected static function getPossibleTypeOptions(?GeneralField $record): array|Collection
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
                    $component = $value->getModifyOptionComponent($key);
                    $label = $component instanceof Field ? $component->getLabel() ?? $key : $key;

                    return [$key => $label];
                } catch (Error) {
                    //When label need record or livewire component
                    return [$key => $key];
                }
            });
        //ToDo may with filtering (Some are not good to select in GeneralField)
    }

    protected static function getGeneralFieldSection(): Section
    {
        return Section::make()
            ->columnSpan(2)
            ->columns()
            ->schema([
                TextInput::make('name')
                    ->label(GeneralField::__('attributes.name.label'))
                    ->helperText(GeneralField::__('attributes.name.helper_text'))
                    ->columnStart(1)
                    ->columnSpan(1),
                Group::make([
                    Select::make('type')
                        ->options(static::getAllCustomFieldTypeOptions())
                        ->helperText(static::helperText(...))
                        ->disabledOn('edit')
                        ->label(static::label(...))
                        ->afterStateUpdated(function ($set) {
                            $set('overwrite_option_keys', []);
                            $set('overwrite_options', []);
                        })
                        ->columnStart(1)
                        ->columnSpan(1)
                        ->searchable()
                        ->required()
                        ->live(),
                    IconPicker::make('icon') //todo ???
                    ->helperText(static::helperText(...))
                        ->label(static::label(...))
                        ->gridSearchResults()
                        ->required(),
                ]),
                TextInput::make('identifier')
                    ->helperText(static::helperText(...))
                    ->label(static::label(...))
                    ->disabledOn('edit')
                    ->columnSpan(1)
                    ->required()
                    ->unique(),
                Toggle::make('is_active')
                    ->helperText(static::helperText(...))
                    ->label(static::label(...))
                    ->default(true)
                    ->columnSpan(1),
            ]);
    }

    protected static function getOverwriteTypeOptions($record): Component
    {
        return Fieldset::make(GeneralField::__('attributes.overwrite_options.label'))
            ->columnSpan(1)
            ->columnStart(2)
            ->columns(1)
            ->hidden(static::hasFieldTypeOptions(...))
            ->schema([
                TextEntry::make('message')
                    ->label(GeneralField::__('attributes.overwrite_options.message_on_create'))
                    ->hiddenOn('edit'),
                Select::make('overwrite_option_keys')
                    ->options(static::getPossibleTypeOptions(...))
                    ->hiddenOn('create')
                    ->columnStart(1)
                    ->columnSpan(1)
                    ->hiddenLabel()
                    ->required()
                    ->multiple()
                    ->live(),
                Group::make()
                    ->hiddenOn('create')
                    ->statePath('overwrite_options')
                    ->schema(static::getOverwrittenOptionDynamicSchema($record)),
            ]);
    }

    protected static function getGeneralFieldTypeOptions(?GeneralField $record): Fieldset
    {
        /**@var CustomFieldType|null $type */
        $type = $record?->getType();
        $schema = [];
        $visible = true;

        if (!is_null($type)) {
            $schema = $type->getGeneralTypeOptionComponents();
            $visible = count($type->generalTypeOptions()) > 0;
        }

        return Fieldset::make(GeneralField::__('attributes.options.label'))
            ->columnSpan(1)
            ->columns(1)
            ->statePath('options')
            ->visible($visible)
            ->schema([
                TextEntry::make('message')
                    ->label(GeneralField::__('attributes.options.message_on_create'))
                    ->hiddenOn('edit'),
                Group::make()
                    ->hiddenOn('create')
                    ->schema($schema),
            ]);
    }

    protected static function helperText(Component $component): string
    {
        return GeneralField::__('attributes.' . $component->getStatePath(false) . '.helper_text');
    }

    protected static function label(Component $component): string
    {
        return GeneralField::__('attributes.' . $component->getStatePath(false) . '.label');
    }

    protected static function getAllCustomFieldTypeOptions(): Collection
    {
        $types = CustomFieldType::getSelectableGeneralFieldTypes();

        return collect($types)->map(fn(string|CustomFieldType $type) => $type::displayname());
    }

    protected static function hasFieldTypeOptions(?GeneralField $record): bool
    {
        if (is_null($record)) {
            return false;
        }

        $type = $record->getType();
        $array = $type->getFlattenExtraTypeOptions();

        return empty($array);
    }
}
