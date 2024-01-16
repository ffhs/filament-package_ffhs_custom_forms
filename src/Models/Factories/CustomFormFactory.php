<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Factories;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;


class CustomFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = CustomForm::class;
    public function definition(): array
    {
        return [
            'short_title' => fake()->name(),
            'custom_form_identifier' => fake()->randomElement(config("ffhs_custom_forms.forms")),
        ];
    }

    public function customFieldsOfAllTypes(): Factory
    {

        return $this
            ->afterCreating(function (CustomForm $form){
                /**@var array $types*/
                $types = $form->getFormConfiguration()::formFieldTypes();
                CustomField::factory(sizeof($types))
                    ->state(new Sequence(
                        fn (Sequence $sequence) => [
                            'type' => array_keys($types)[$sequence->index],
                            'name_de' => (array_values($types)[$sequence->index])::getFieldIdentifier(),
                            'name_en' => (array_values($types)[$sequence->index])::getFieldIdentifier(),
                            'form_position' => $sequence->index+1,
                            'layout_end_position' => is_a (array_values($types)[$sequence->index], CustomLayoutType::class, true) ? $sequence->index+1: null
                        ],
                    ))
                    ->state(["custom_form_id"=> $form->id])
                    ->afterCreating(function (CustomField $field){
                        $variation = new CustomFieldVariation();
                        $variation->custom_field_id = $field->id;
                        $variation->required = true;
                        $variation->is_active = true;
                        $variation->options = $field->getType()->getExtraOptionFields();
                        $variation->save();
                    })->create();
            });

    }


    public function generalFields(): Factory
    {
        return $this
            ->afterCreating(function (CustomForm $form){
                $generalFields = GeneralFieldForm::getGeneralFieldQuery($form->custom_form_identifier)->get();
                CustomField::factory($generalFields->count())
                    ->state(new Sequence(
                        fn (Sequence $sequence) => [
                            'general_field_id' => $generalFields->toArray()[$sequence->index],
                            'custom_form_id' => $form->id,
                        ],
                    ))
                    ->fromGeneralField()->afterCreating(function (CustomField $field){
                        $variation = new CustomFieldVariation();
                        $variation->custom_field_id = $field->id;
                        $variation->options = $field->getType()->getExtraOptionFields();
                        $variation->save();
                    })->create();
            });

    }


}
