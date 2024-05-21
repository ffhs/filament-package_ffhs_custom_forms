<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

final class CustomFieldAdder extends FormEditorFieldAdder
{
    function getTitle(): string {
        return  __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.custom_fields");
    }

    /**
     * Returns a list of actions for every active custom field.
     * Clicking an action opens a modal to configure that
     * custom field and finally add it to the form
     *
     * @return array
     */
    function getSchema(): array {
        $actions = [];
        $types = collect($this->form->getFormConfiguration()::formFieldTypes())->map(fn($class) => new $class());


        /**@var CustomFieldType $type */
        foreach ($types as $type) {
            $modalWidth  = CustomFieldEditModal::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]);

            $actions[] = Actions::make([
                Action::make("add_".$type::getFieldIdentifier()."_action")
                    ->mutateFormDataUsing(fn(Action $action)=> CustomFormEditorHelper::getRawStateForm($action,1))
                    ->modalHeading(__("filament-package_ffhs_custom_forms::custom_forms.form.compiler.add_a_name_field",['name'=>$type->getTranslatedName()]))
                    ->disabled(fn(Get $get) => is_null($type::getFieldIdentifier()))
                    ->extraAttributes(["style" => "width: 100%; height: 100%;"])
                    ->label(self::getCustomFieldAddActionLabel($type))
                    ->closeModalByClickingAway(false)
                    ->tooltip($type->getTranslatedName())
                    ->modalWidth($modalWidth)
                    ->outlined()
                    ->form(function() use ($type) {
                        $state = ["type" => $type::getFieldIdentifier()];
                        return [CustomFieldEditModal::make($this->form, $state)];
                    })
                    ->action(function ($set, Get $get, array $data) {
                        $this->addCustomFieldInRepeater($data, $get, $set);
                    })
                    ->fillForm(fn($get) => [
                        "type" => $type::getFieldIdentifier(),
                        "options" => $type->getDefaultTypeOptionValues(),
                        "is_active" => true,
                        "identify_key" => uniqid(),
                    ]),
            ]);
        }

        return [
            Group::make($actions)->columns()
        ];
    }


    private static function getCustomFieldAddActionLabel(CustomFieldType $type):HtmlString {
        $html =
            '<div class="flex flex-col items-center justify-center">'. //
            Blade::render('<x-'.$type->icon().' class="h-6 w-6"/>').
            '<p class="" style="margin-top: 10px;word-break: break-word;">'.$type->getTranslatedName().'</p>'.
            '</div>';

        return  new HtmlString($html);
    }

}
