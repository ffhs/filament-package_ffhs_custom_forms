<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\EmbeddedCustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class CustomFormEditor extends Field implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;
    use CanSaveCustomFormEditorData;
    use CanLoadCustomFormEditorData;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-editor';

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull()
            ->columns(1);

        $this->schema([
            Tabs::make()
                ->extraAttributes(["class" => "overflow-y-auto scroll-smooth"])
                ->columnSpanFull()
                ->tabs([
                    $this->getFormTab(),
                    $this->getRuleTab()
                ]),

        ]);
        $this->mutateRelationshipDataBeforeFillUsing(function (array $data) {
            $customForm = CustomForm::cached($data["id"]);
            if (is_null($customForm)) {
                $customForm = new CustomForm();
            }
            return $this->loadCustomFormEditorData($customForm);
        });

        $this->saveRelationshipsUsing(function ($state, EmbeddedCustomFormEditor $component) {
            $form = $component->getRelationship()->first();
            $this->saveCustomFormEditorData($state, $form);
        });
    }

    public function getFormTab(): Tab
    {
        return Tab::make("Formular") //ToDo Translate
        ->icon("carbon-data-format")
            ->columns(6)
            ->schema([
                Fieldset::make()
                    ->columnStart(1)
                    ->columnSpan(1)
                    ->columns(1)
                    ->schema(fn($record) => //ToDo Remove Closure
                    $record ?
                        collect($record->getFormConfiguration()::editorFieldAdder())
                            ->map(fn(string $class) => $class::make())
                            ->toArray() : []
                    ),

                EditCustomFields::make("custom_fields")
                    ->columnStart(2)
                    ->columnSpan(5),

            ]);
    }

    public function getRuleTab(): Tab
    {
        return Tab::make("Regeln") //ToDo Translate
        ->icon("carbon-rule-draft")
            ->schema([
                RuleEditor::make()
                    ->columnSpanFull()
            ]);
    }

    /*   public function getCachedExistingRecord(): ?Model
   {
       //Replace it with cached
       $forginkey = $this->getRelationship()->getForeignKeyName();
       $value =  $this->getRelationship()->getChild()->$forginkey;
       $onwerKey = $this->getRelationship()->getOwnerKeyName();
       return CustomForms::cached($value,$onwerKey);
   }*/
}
