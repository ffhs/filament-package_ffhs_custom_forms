<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\Helper\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomForm extends EditRecord
{
    protected static string $resource = CustomFormResource::class;



    public function form(Form $form): Form {
        return $form->schema([Section::make()->schema([CustomFormEditor::make()])]);
    }

    protected function fillForm(): void {
        //$this->form->fill(['form' => EditCustomFormLoadHelper::load($this->getRecord())]);

        $id1 = uniqid();
        $id2 = uniqid();
        $id3 = uniqid();
        $id4 = uniqid();
        $id4_1 = uniqid();
        $id4_2 = uniqid();
        $id5 = uniqid();

        $this->form->fill([
            'custom_fields' => [
                'structure' => [
                    $id1 => [],
                    $id2 => [],
                    $id3 => [],
                    $id4 => [
                                $id4_1=>[],
                                $id4_2=>[]
                            ],
                    $id5 => [],
                ],

                'data' => [
                    $id1 => ['name' => "Test1", "type" => TextType::identifier()],
                    $id2 => ['name' => "Test2", "type" => SectionType::identifier()],
                    $id3 => ['name' => "Test3", "type" => TextType::identifier()],
                    $id4 => ['name' => "Test4", "type" => GroupType::identifier(),],
                    $id4_1 => ['name' => "Test5", "type" => TextType::identifier()],
                    $id4_2 => ['name' => "Test5", "type" => TextType::identifier()],
                    $id5 => ['name' => "Test5", "type" => TextType::identifier()],
                ],
            ]
        ]);
    }


    public function getTitle(): string|Htmlable {
        return $this->record->short_title . " - " . parent::getTitle();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }





}
