<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\{Hidden, Repeater};
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


         if($ordered) $schema = [
                 ...$parameter["rendered"],
                Hidden::make("order")
        ];
         else $schema = $parameter["rendered"];


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
        if($ordered) $answares = collect($answares)->sortBy("order");


        /** @var Collection $fields */
        $fields = $parameter["customFieldData"];
        $fields = $fields->keyBy("form_position");
        $offset = $fields->sortBy("form_position")->first()->form_position -1;
        $viewMode = $parameter["viewMode"];
        $customForm = $record->customFormAnswer->customForm;


        $schema = [];
        foreach ($answares as $id => $answer) {

            $answaresFields = collect($answer)->map(function ($value, $key) use ($record, $fields) {
                return  (new CustomFieldAnswer())
                    ->fill([
                        "custom_field_id" => $fields->firstWhere("identifier", $key),
                        "custom_form_answer_id" => $record->custom_form_answer_id,
                        "answer" => $value,
                    ]);
            });

            $render = CustomFormRender::getInfolistRender(
                $parameter["viewMode"],
                $customForm,
                $record->customFormAnswer,
                $answaresFields
            );

            /** @var Collection $subSchema */
            $subSchema = CustomFormRender::render($offset, $fields, $render, $viewMode, $customForm)[2];
            foreach ($subSchema as $subSchemaKey => $subSchemaValue) {
                $subSchemaValue->getStateUsing(fn() => $answer[$subSchemaKey]);
            }

            $schema[] = Fieldset::make("")
                ->schema(array_values($subSchema))
                ->state(fn() => $answer)
                ->statePath($id);
        }


        return $component
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }

}
