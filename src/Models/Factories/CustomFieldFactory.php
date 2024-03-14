<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models\Factories;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;


class CustomFieldFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = CustomField::class;
    public function definition(): array
    {
        return [
            'name_de' => fake()->name,
            'name_en' => fake()->name,
            'identify_key'=> uniqid(),
            'tool_tip_de' => fake()->text(20),
            'tool_tip_en' => fake()->text(20),
            'type'=> fake()->randomElement(CustomFieldType::getAllTypes()),
        ];
    }


    public function fromGeneralField():Factory {
        return $this->state([
            'name_de' => null,
            'name_en' => null,
            'tool_tip_de' => null,
            'tool_tip_en' => null,
        ]);
    }



}
