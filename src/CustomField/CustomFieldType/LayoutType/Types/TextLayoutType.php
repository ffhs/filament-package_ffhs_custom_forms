<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\TextLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Components\Tab;

class TextLayoutType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

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
