<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropActions;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents\FormEditorFieldAdder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

final class CustomFieldTypeAdder extends FormEditorFieldAdder
{
    protected function setUp(): void {
        parent::setUp();
        $this->live();
        $this->label(__("filament-package_ffhs_custom_forms::custom_forms.form.compiler.custom_fields"));


        $this->schema($this->setUpActions(...));
    }



private static function getCustomFieldAddActionLabel(CustomFieldType $type):HtmlString {
    $html =
        '<div class="flex flex-col items-center justify-center">'.
            new HtmlString(Blade::render("<x-".$type->icon() . ' class="w-6 h-6" />')).
            '<span style="margin-top: 2px;  text-align: center;"> '. $type->getTranslatedName().'</span>
        </div>';

    return  new HtmlString($html);
}


    public function getTypes(): array {
        return collect($this->getRecord()->getFormConfiguration()::formFieldTypes())
            ->map(fn($class) => new $class())->toArray();
    }

    public function setUpActions(): array
    {
        $actions = [];
        foreach ($this->getTypes() as $type) {
            /**@var CustomFieldType $type */
            // $modalWidth  = CustomFieldEditModal::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]);

            $actions[] = DragDropActions::make([
                Action::make("add_" . $type::identifier() . "_action")
                    ->extraAttributes(["style" => "width: 100%; height: 100%;"])
                    ->label(self::getCustomFieldAddActionLabel($type))
                    ->tooltip($type->getTranslatedName())
                    ->outlined()
                    ->action(function ($arguments, $component) use ($type) {
                        $field = [
                            "identifier" => uniqid(),
                            "type" => $type::identifier(),
                            "options" => $type->getDefaultTypeOptionValues(),
                            "is_active" => true,
                        ];

                        $this::addNewField($component, $arguments, $field);
                    }),
            ])->dragDropGroup('custom_fields');
        }
        return [Group::make($actions)->columns()];
    }
}
