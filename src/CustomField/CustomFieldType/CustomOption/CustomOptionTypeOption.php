<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Cache;

class CustomOptionTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    //toDo Change

    public function getDefaultValue(): array
    {
        return [];
    }

    public function getComponent(string $name): Component
    {
        return Group::make()
            ->columnSpanFull()
            ->schema([
                Group::make()
                    ->schema(
                        fn($get) => is_null($get("../general_field_id")) ? [] : [$this->getCustomOptionsSelector($name)]
                    )
                    ->hidden(fn($get) => is_null($get("../general_field_id")))
                    ->columnSpanFull(),
                Group::make()
                    ->schema(
                        fn($get) => is_null($get("../general_field_id")) ? [$this->getCustomOptionsRepeater($name)] : []
                    )
                    ->visible(fn($get) => is_null($get("../general_field_id")))
                    ->columnSpanFull(),
            ]);
    }

    private function getCustomOptionsSelector($name): Component
    {
        return Select::make($name)
            ->label("Mögliche Auswahlmöglichkeiten")
            ->columnSpanFull()
            ->multiple()
            ->options(function ($get) {
                $generalField = GeneralField::cached($get("../general_field_id"));
                return $generalField->customOptions->pluck("name", "id"); //toDo Translate;
            });
    }

    private function getCustomOptionsRepeater($name): Repeater
    {
        return Repeater::make($name)
            ->collapseAllAction(fn($action) => $action->hidden())
            ->expandAllAction(fn($action) => $action->hidden())
            ->itemLabel(fn($state, $record) => $state["name"][$record->getLocale()]) //ToDo Translate
            ->label("Feldoptionen") //ToDo Translate
            ->columnSpanFull()
            ->collapsible()
            ->collapsed()
            ->addable()
            ->columns()
            ->afterStateUpdated(function ($set, array $state) use ($name) {
                foreach (array_keys($state) as $optionKey) {
                    if (empty($state[$optionKey]["identifier"])) $state[$optionKey]["identifier"] = uniqid();
                }
                $set($name, $state);
            })->schema(fn($record) => [
                TextInput::make("name." . $record->getLocale())
                    ->label("Name")
                    ->required(),
                TextInput::make("identifier")
                    ->label("Identifikator")
                    ->required(),
            ]);
    }

    public function mutateOnFieldSave(mixed $data, string $key, CustomField $field): null
    {
        Cache::set($this->getCachedOptionEditSaveKey($field), $data, 10);
        return null;
    }

    /**
     * @param CustomField $field
     *
     * @return string
     */
    public function getCachedOptionEditSaveKey(CustomField $field): string
    {
        return "custom_field-custom_options-" . $field->identifier . "-user_" . auth()->id();
    }

    public function afterSaveField(mixed &$data, string $key, CustomField $field): void
    {
        if ($field->isGeneralField()) {
            $field->customOptions()->sync($data);
            return;
        }

        $data = Cache::get($this->getCachedOptionEditSaveKey($field));
        $ids = [];
        $toCreate = [];
        if (is_null($data)) $data = [];
        foreach ($data as $optionData) {
            if (!array_key_exists("id", $optionData)) {
                if (empty($optionData["identifier"])) {
                    $optionData = array_merge($optionData, ["identifier" => uniqid()]
                    );
                }
                $toCreate[] = $optionData;
                continue;
            }
            $ids[] = $optionData["id"];
            $field->customOptions->where("id", $optionData["id"])->first()?->update($optionData);
        }

        $field->customOptions()->whereNotIn("custom_options.id", $ids)->delete();
        $field->customOptions()->createMany($toCreate);
    }

    public function mutateOnFieldLoad(mixed $data, string $key, CustomField $field): mixed
    {
        //if(!is_null($value) &&!isEmpty($value))return $value;
        if ($field->isGeneralField()) return $field->customOptions->pluck("id")->toArray();
        $field->customOptions->each(function (CustomOption $option) use (&$value) {
            $value["record-" . $option->id] = $option->toArray();
        });
        return $value;
    }

    public function mutateOnFieldClone(mixed &$data, int|string $key, CustomField $original): mixed
    {
        if ($original->isGeneralField()) return parent::mutateOnFieldClone($data, $key, $original);
        $options = [];
        foreach ($original->customOptions as $customOption) {
            /**@var CustomOption $customOption */
            $customOptionData = $customOption->toArray();
            unset($customOptionData["id"]);
            unset($customOptionData["created_at"]);
            unset($customOptionData["deleted_at"]);
            unset($customOptionData["updated_at"]);
            unset($customOptionData["pivot"]);
            $options[uniqid()] = $customOptionData;
        }
        return parent::mutateOnFieldClone($options, $key, $original);
    }


}
