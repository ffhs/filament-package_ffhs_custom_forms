<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutTypeDefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class SectionType extends CustomLayoutType
{

    use HasCustomTypePackageTranslation;

    public static function identifier(): string {
        return "section";
    }

    public function viewModes(): array {
        return [
            "default" => SectionTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-section";
    }

    public function extraTypeOptions(): array {
        return[
            LayoutTypeDefaultLayoutTypeOptionGroup::make()
                ->removeTypeOption("helper_text")
                ->mergeTypeOptions([
                    'show_in_view'=> ShowInViewOption::make()
                        ->modifyComponent(fn(Component $component) => $component->columnStart(1)),
                    'show_as_fieldset' => ShowAsFieldsetOption::make()
                        ->modifyComponent(fn(Component $component) => $component->columnStart(2)),
                    "show_title" =>  ShowTitleOption::make(),
                    'aside' => new FastTypeOption(false,
                        Toggle::make("aside")
                            ->label("Titel seitlich Anzeigen") //ToDo Translate,
                            ->disabled(fn($get) => !$get("show_title"))
                    ),
                ]),
        ];
    }

}
