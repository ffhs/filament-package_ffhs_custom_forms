<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper\EditCustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomForm extends EditRecord
{
    use Translatable;
    protected static string $resource = CustomFormResource::class;


    public function getMaxContentWidth(): MaxWidth|string|null {
        return MaxWidth::Full;
    }

    public function form(Form $form): Form {
        return $form->schema([Section::make()->schema([CustomFormEditor::make()])]);
    }

    protected function fillForm(): void {
       $this->form->fill(EditCustomFormLoadHelper::load($this->getRecord()));

       /* $this->form->fill([
            'test1' => [
                "element-test1" => [
                    "wtf1" => "test",
                    "wtf2" => "test",
                    'form_position' => 1,
                    'layout_end_position' => 1,
                    'subTest1'=> [
                        'sub1'=>[
                            'wtf1'=> 'sub1',
                            'subTest1'=> [],
                        ],
                        'sub2'=>[
                            'wtf1'=> 'sub2',
                            'subTest1'=> [],
                        ],
                    ]
                ],
                "element-test2" => [
                    "wtf1" => "test2",
                    "wtf2" => "test2",
                    'form_position' => 2,
                    'subTest1'=> [],
                ]
            ],


            'test2' => [
                "element-test3" => [
                    "wtf3" => "test3",
                    "wtf4" => "test3",
                    'form_position' => 1,
                    'layout_end_position' => 2,
                    'subTest1'=> [],
                ],
                "element-test4" => [
                    "wtf5" => "test3",
                    "wtf6" => "test3",
                    'form_position' => 2,
                    'subTest1'=> [],
                ]
            ]
        ]); */
    }

    public function getTitle(): string|Htmlable {
        return $this->record->short_title . " - " . parent::getTitle();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }


    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void {

        $this->authorizeAccess();

        EditCustomFormSaveHelper::save($this->data["custom_fields"], $this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);

    }


}
