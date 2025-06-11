<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\TextLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\App;

class TextLayoutType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "layout_text";
    }

    public function viewModes(): array
    {
        return [
            'default' => TextLayoutTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "heroicon-m-chat-bubble-bottom-center-text";
    }


    public function extraTypeOptions(): array
    {
        $buttons = [
            'bold',
            'bulletList',
            'italic',
            'link',
            'orderedList',
            'underline',
        ];

        return [
            LayoutOptionGroup::make()
                ->addTypeOptions('show_in_view', ShowInViewOption::make())
                ->removeTypeOption("helper_text")
                ->addTypeOptions(
                    'text',
                    FastTypeOption::makeFast(
                        "",
                        RichEditor::make("text." . App::getLocale())
                            ->columnSpanFull()
                            ->toolbarButtons(
                                $buttons
                            ) //ToDo Add Location Selection, add to FieldMapper the language Getter
                            ->label("Text")
                    )
                ),
        ];
    }


}
