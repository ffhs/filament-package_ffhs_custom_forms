<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Factories;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Database\Eloquent\Factories\Factory;


class GeneralFieldFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = GeneralField::class;
    public function definition(): array
    {
        return [
            'name_de' => fake()->name,
            'name_en' => fake()->name,
            'identify_key'=> uniqid(),
            'tool_tip_de' => fake()->text(20),
            'tool_tip_en' => fake()->text(20),
            'type'=> fake()->randomElement(CustomFieldType::getAllTypes()),
            'is_active'=> true,
        ];
    }


    public function bindToAllForms():Factory {
        return $this->afterCreating(function(GeneralField $field) {
            collect(config("ffhs_custom_forms.forms"))
                ->map(fn(string $class) => $class::identifier())
                ->each(fn(string $identifier) => (new GeneralFieldForm())
                ->fill([
                    'custom_form_identifier' => $identifier,
                    'general_field_id'=> $field->id
                ])
                ->save()
        );


        });
    }



}
