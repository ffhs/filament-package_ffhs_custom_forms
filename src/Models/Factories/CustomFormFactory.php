<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Factories;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
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
                $types = $form->getFormConfiguration()::formFieldTypes();
                CustomField::factory(sizeof($types))
                    ->state(new Sequence(
                        fn (Sequence $sequence) => [
                            'type' => (array_values($types)[$sequence->index])::identifier(),
                            'name' => fake()->name,
                            'is_active' => true,
                            'layout_end_position' => is_a (array_values($types)[$sequence->index], CustomLayoutType::class, true) ? $sequence->index+1: null,
                            'form_position' => $sequence->index+1,
                        ],
                    ))
                    ->state(["custom_form_id"=> $form->id])
                    ->create();
            });

    }


   /* public function generalFields(): Factory
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

    }*/


}
