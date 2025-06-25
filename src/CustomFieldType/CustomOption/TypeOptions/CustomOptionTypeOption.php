<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class CustomOptionTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getDefaultValue(): array
    {
        return [];
    }

    public function getComponent(string $name): Component
    {
        return Group::make()
            ->columnSpanFull()
            ->schema(function ($get) use ($name) {
                return once(function () use ($name, $get) {
                    if (is_null($get('../general_field_id'))) {
                        return [$this->getCustomOptionsRepeater($name)];
                    } else {
                        return [$this->getCustomOptionsSelector($name)];
                    }
                });
            });
    }

    public function mutateOnFieldSave(mixed $data, string $key, CustomField $field): mixed
    {
        return null;
    }

    public function afterSaveField(mixed &$data, string $key, CustomField $field): void
    {
        if ($field->isGeneralField()) {
            $field->customOptions()->sync($data);
            return;
        }

        $ids = [];
        $toCreate = [];
        $data = $data ?? [];
        foreach ($data as $optionData) {
            if (!isset($optionData['id'])) {
                if (empty($optionData['identifier'])) {
                    $optionData['identifier'] = uniqid();
                }
                $toCreate[] = $optionData;
                continue;
            }
            $ids[] = $optionData['id'];
            $field->customOptions->where('id', $optionData['id'])->first()?->update($optionData);
        }

        $field->customOptions()->whereNotIn('custom_options.id', $ids)->delete();
        $field->customOptions()->createMany($toCreate);
    }

    public function mutateOnFieldLoad(mixed $data, string $key, CustomField $field): mixed
    {
        if ($field->isGeneralField()) {
            return $field->customOptions->pluck('id')->toArray();
        }
        $field->customOptions->each(function (CustomOption $option) use (&$value) {
            $value['record-' . $option->id] = $option->toArray();
        });
        return $value;
    }

    public function mutateOnFieldClone(mixed &$data, int|string $key, CustomField $original): mixed
    {
        if ($original->isGeneralField()) {
            return parent::mutateOnFieldClone($data, $key, $original);
        }
        $options = [];
        foreach ($original->customOptions as $customOption) {
            /**@var CustomOption $customOption */
            $customOptionData = $customOption->toArray();
            unset(
                $customOptionData['id'],
                $customOptionData['created_at'],
                $customOptionData['deleted_at'],
                $customOptionData['updated_at'],
                $customOptionData['pivot']
            );
            $options[uniqid()] = $customOptionData;
        }
        return parent::mutateOnFieldClone($options, $key, $original);
    }

    public function canBeOverwrittenByNonField(): bool
    {
        return false;
    }

    protected function getCustomOptionsSelector($name): Component
    {
        return Select::make($name)
            ->label(CustomOption::__('possible_options.label'))
            ->helperText(CustomOption::__('possible_options.helper_text'))
            ->columnSpanFull()
            ->multiple()
            ->options(function ($get) {
                return once(function () use ($get) {
                    $generalField = GeneralField::firstWhere('id', $get('../general_field_id'));
                    return $generalField->customOptions->pluck('name', 'id')->toArray();
                });
            });
    }

    private function getCustomOptionsRepeater($name): Repeater
    {
        return Repeater::make($name)
            ->collapseAllAction(fn($action) => $action->hidden())
            ->expandAllAction(fn($action) => $action->hidden())
            ->itemLabel(fn($state, $record) => $state['name'][$record->getLocale()])
            ->label(CustomOption::__('label.multiple'))
            ->hidden(function ($get, $set) use ($name) {
                if (is_null($get($name))) {
                    $set($name, []);
                }
            })
            ->columnSpanFull()
            ->collapsible()
            ->collapsed()
            ->addable()
            ->columns()
            ->afterStateUpdated(function ($set, array $state) use ($name) {
                foreach (array_keys($state) as $optionKey) {
                    if (empty($state[$optionKey]['identifier'])) {
                        $state[$optionKey]['identifier'] = uniqid();
                    }
                }
                $set($name, $state);
            })
            ->schema(fn($record) => once(fn() => [
                TextInput::make('name.' . $record->getLocale())
                    ->label(CustomOption::__('name.label'))
                    ->helperText(CustomOption::__('identifier.helper_text'))
                    ->required(),
                TextInput::make('identifier')
                    ->label(CustomOption::__('identifier.label'))
                    ->helperText(CustomOption::__('identifier.helper_text'))
                    ->required(),
            ]));
    }
}

