<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\{Repeater};
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Collection;


class RepeaterLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array  $parameter = []): \Filament\Forms\Components\Component {


        $ordered = FieldMapper::getOptionParameter($record,'ordered');
        $minAmount = FieldMapper::getOptionParameter($record,'min_amount');
        $maxAmount = FieldMapper::getOptionParameter($record,'max_amount');
        $defaultAmount = FieldMapper::getOptionParameter($record,'default_amount');

        $schema = $parameter["renderer"]();


        /**@var \Filament\Forms\Components\Repeater $repeater*/
        $repeater = static::makeComponent(Repeater::class, $record);
        $repeater
            ->columnSpan(FieldMapper::getOptionParameter($record,"column_span"))
            ->columns(FieldMapper::getOptionParameter($record,"columns"))
            ->columnStart(FieldMapper::getOptionParameter($record,"new_line_option"))
            ->schema($schema)
            ->minItems($minAmount)
            ->maxItems($maxAmount)
            ->defaultItems($defaultAmount);

        if($ordered) $repeater->orderColumn("order");
        else $repeater->reorderable(false);


        return $repeater;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,  array  $parameter = []): \Filament\Infolists\Components\Component
    {
        $ordered = FieldMapper::getOptionParameter($record,'ordered');

        $isFieldset = FieldMapper::getOptionParameter($record,"show_as_fieldset");
        $component = $isFieldset
            ? Fieldset::make(FieldMapper::getLabelName($record))
            : Section::make(FieldMapper::getLabelName($record));

        $answares = FieldMapper::getAnswer($record);
        if($ordered) $answares = collect($answares)->sortBy("order"); //ToDo not work


        /** @var Collection $fields */
        $fields = $parameter["customFieldData"];
        $fields = $fields->keyBy("form_position");
        $offset = $fields->sortBy("form_position")->first()->form_position -1;
        $viewMode = $parameter["viewMode"];
        $customForm = $record->customFormAnswer->customForm;


        $schema = [];
        foreach ($answares as $id => $answer) {

            unset($answer["order"]);
            $answaresFields = collect($answer)->map(function ($value, $key) use ($record, $fields) {

                $customField = $fields->firstWhere("identifier", $key)?->id;
                if(is_null($customField)) return null;

                $field =  (new CustomFieldAnswer())
                    ->fill([
                        "custom_field_id" => $customField,
                        "custom_form_answer_id" => $record->custom_form_answer_id,
                    ]);
                $field->answer = $field->customField->getType()->prepareSaveFieldData($value);
                return $field;
            })
                ->whereNotNull()
                ->keyBy(function (CustomFieldAnswer $answer) {return $answer->customField->identifier;});

           // dd($answaresFields);


            $render = CustomFormRender::getInfolistRender(
                $parameter["viewMode"],
                $customForm,
                $record->customFormAnswer,
                $answaresFields
            );


            $renderOutput = CustomFormRender::renderRaw($offset, $fields, $render, $viewMode, $customForm);
            $subSchema = $renderOutput[0];
            $allComponents = $renderOutput[1];
            $parameter["registerComponents"]($allComponents);

            $schema[] = Fieldset::make("")
                ->schema($subSchema)
                ->state(fn() => $answer)
                ->statePath($id);
        }

        return $component
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }

}
