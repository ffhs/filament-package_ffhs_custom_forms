<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\TextLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\App;

class TextLayoutType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string {
        return "layout_text";
    }

    public function viewModes(): array {
        return [
          'default' => TextLayoutTypeView::class
        ];
    }

    public function icon(): string {
       return "heroicon-m-chat-bubble-bottom-center-text";
    }



    public function extraTypeOptions(): array {

        $buttons = [
            'bold',
            'bulletList',
            'italic',
            'link',
            'orderedList',
            'underline',
        ];

        return [
            DefaultLayoutTypeOptionGroup::make()
                ->addTypeOptions('show_in_view', ShowInViewOption::make())
                ->addTypeOptions('text',
                    FastTypeOption::makeFast("",
                        RichEditor::make("text." . App::getLocale())
                            ->columnSpanFull()
                            ->toolbarButtons($buttons) //ToDo Add Location Selection, add to FormMapper the language Getter
                            ->label("Text")
                    )
                ),
        ];
    }


}
