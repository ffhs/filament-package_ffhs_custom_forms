<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\TextLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Components\Tab;

class TextLayoutType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {
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


    protected function extraOptionsAfterBasic(): array {
        $buttons = [
            'bold',
            'bulletList',
            'italic',
            'link',
            'orderedList',
            'underline',
        ];

        return [
            'show_in_view'=> new ShowInViewOption(),
            'text'=> new FastTypeOption("",
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make("Text Deutsch")
                            ->schema([RichEditor::make("text_de")->toolbarButtons($buttons)->label("")]),
                        Tabs\Tab::make("Text Englisch")
                            ->schema([RichEditor::make("text_en")->toolbarButtons($buttons)->label("")]),
                    ])
            )
        ];
    }

    public function canBeRequired(): bool {
        return false;
    }

    public function hasToolTips(): bool {
        return false;
    }
}
