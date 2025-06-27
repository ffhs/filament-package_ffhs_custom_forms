<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

final class CustomFieldTypeAdder extends FormEditorFieldAdder
{
    private static function getCustomFieldAddActionLabel(CustomFieldType $type): HtmlString
    {
//        return new HtmlString(htmlspecialchars($type->getTranslatedName()));

        $html =
            '<div class="flex flex-col items-center justify-center">' .
            Blade::render('<x-' . $type->icon() . ' class="w-6 h-6" />') .
            '<span style="margin-top: 2px;  text-align: center;"> ' . htmlspecialchars($type->getTranslatedName()) .
            '</span> </div>';

        return new HtmlString($html);
    }

    public function setUpSchema(): array
    {
        $actions = [];
        foreach ($this->getTypes() as $type) {
            /**@var CustomFieldType $type */

            $actions[] =
                DragDropAction::make('add_' . $type::identifier() . '_action')
                    ->extraAttributes(['style' => 'width: 7rem; height: 100%;'])
                    ->label(self::getCustomFieldAddActionLabel($type))
                    ->tooltip($type->getTranslatedName())
                    ->outlined()
                    ->action(function ($arguments, $component, EditRecord $livewire) use ($type) {
                        $field = $this->getNewFieldData($type);
                        $this::addNewField($component, $arguments, $livewire, $field);
                    });
        }

        return [
            DragDropActions::make($actions)
                ->dragDropGroup(fn(Get $get) => 'custom_fields-' . $get('custom_form_identifier'))
        ];
    }

    public function getTypes(): array
    {
        /**@var CustomForm|null $customForm */
        $formIdentifier = $this->getGetCallback()('custom_form_identifier');
        if (is_null($formIdentifier)) {
            return [];
        }

        $formConfiguration = CustomForms::getFormConfiguration($formIdentifier);
        $fieldTypes = $formConfiguration::formFieldTypes();
        return collect($fieldTypes)->map(fn($class) => app($class))->toArray();
    }

    protected function getNewFieldData(CustomFieldType $type): array
    {
        return [
            'identifier' => uniqid(),
            'type' => $type::identifier(),
            'options' => $type->getDefaultTypeOptionValues(),
            'is_active' => true,
            'name' => [
                app()->getLocale() => CustomForm::__('pages.type_adder.new_field_name')
            ]
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->live();
        $this->label(CustomForm::__('pages.type_adder.label'));
        $this->schema($this->setUpSchema(...));
    }
}
