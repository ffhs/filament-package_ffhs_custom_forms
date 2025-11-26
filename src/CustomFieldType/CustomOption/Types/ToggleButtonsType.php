<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views\ToggleButtonsView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\GroupedOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InlineOption;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;

class ToggleButtonsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'toggle_buttons';
    }

    public function viewModes(): array
    {
        return [
            'default' => ToggleButtonsView::class,
        ];
    }

    public function prepareToSaveAnswerData(EmbedCustomFieldAnswer $answer, mixed $data): ?array
    {
        if ($data === '0') {
            $data = false;
        }
        return parent::prepareToSaveAnswerData($answer, $data);
    }

    public function icon(): string
    {
        return 'bi-toggles';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make()
                ->mergeTypeOptions([
                    'inline' => InlineOption::make()
                        ->modifyOptionComponent(function (Toggle $component) {
                            return $component->hidden(fn($get) => $get('grouped'));
                        }),
                    'grouped' => GroupedOption::make(),
                    'boolean' => BooleanOption::make()
                        ->modifyOptionComponent(fn(Toggle $component) => $component
                            ->disabled(fn($get) => $get('multiple'))
                            ->live(),
                        ),
                ]),
            ValidationTypeOptionGroup::make(),
            CustomOptionGroup::make()
                ->setTypeOptions([
                    'customOptions' => CustomOptionTypeOption::make()
                        ->modifyOptionComponent(fn(Component $component) => $component->hidden(fn($get
                        ) => $get('boolean')))
                ]),
        ];
    }

}
