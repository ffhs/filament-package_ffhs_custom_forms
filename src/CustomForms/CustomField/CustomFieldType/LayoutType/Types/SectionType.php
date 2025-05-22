<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutTypeLayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class SectionType extends CustomLayoutType
{

    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "section";
    }

    public function viewModes(): array
    {
        return [
            "default" => SectionTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "tabler-section";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutTypeLayoutOptionGroup::make()
                ->mergeTypeOptions([
                    'show_in_view' => ShowInViewOption::make()
                        ->modifyOptionComponent(fn(Component $component) => $component->columnStart(1)),
                    'show_as_fieldset' => ShowAsFieldsetOption::make()
                        ->modifyOptionComponent(fn(Component $component) => $component->columnStart(2)),
                    "show_label" => ShowLabelOption::make(),
                    'aside' => new FastTypeOption(
                        false,
                        Toggle::make("aside")
                            ->label("Titel seitlich Anzeigen") //ToDo Translate,
                            ->disabled(fn($get) => !$get("show_label"))
                    ),
                ]),
        ];
    }

}
