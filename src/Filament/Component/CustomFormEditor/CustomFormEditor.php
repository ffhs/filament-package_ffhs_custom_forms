<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class CustomFormEditor extends Field implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;
    use CanSaveCustomFormEditorData;
    use CanLoadCustomFormEditorData;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-editor.index';

    public function getFormTab(): Tab
    {
        return Tab::make(CustomForm::__('label.single'))
            ->icon('carbon-data-format')
            ->columns(6)
            ->schema([
                FieldAdders::make(),

                FieldDragDropEditor::make('custom_fields')
                    ->label('')
                    ->columnStart(2)
                    ->columnSpan(5),
            ]);
    }

    public function getRuleTab(): Tab
    {
        return Tab::make(Rule::__('label.multiple'))
            ->icon('carbon-rule-draft')
            ->schema([
                RuleEditor::make()
                    ->columnSpanFull()
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull();
        $this->columns(1);

        $this->schema([
            Tabs::make()
                ->extraAttributes(['class' => 'overflow-y-auto scroll-smooth'])
                ->columnSpanFull()
                ->tabs([
                    $this->getFormTab(),
                    $this->getRuleTab()
                ]),
        ]);

        $this->setupRelationships();
    }

    protected function setupRelationships(): void
    {
        $this->mutateRelationshipDataBeforeFillUsing(function () {
            $customForm = $this->getCachedExistingRecord();
            if (is_null($customForm)) {
                $customForm = new CustomForm();
            }
            return $this->loadCustomFormEditorData($customForm);
        });

        $this->saveRelationshipsUsing(function ($state, CustomFormEditor $component) {
            /**@var CustomForm $customForm */
            $customForm = $component->getCachedExistingRecord();
            $this->saveCustomFormEditorData($state, $customForm);
        });
    }
}
