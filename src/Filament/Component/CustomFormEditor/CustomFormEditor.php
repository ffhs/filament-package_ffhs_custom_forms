<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Closure;
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
    use EntanglesStateWithSingularRelationship {
        EntanglesStateWithSingularRelationship::relationship as traitRelationship;
    }
    use CanSaveCustomFormEditorData;
    use CanLoadCustomFormEditorData;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-editor.index';

    public function getFormTab(): Tab
    {
        return Tab::make(CustomForm::__('label.single'))
            ->icon('carbon-data-format')
            ->columns([
                'xl' => 5,
                '2xl' => 6
            ])
            ->schema([
                FieldAdders::make(),
                FieldDragDropEditor::make('custom_fields')
                    ->label('')
                    ->columnStart(2)
                    ->columnSpan([
                        'xl' => 4,
                        '2xl' => 5
                    ]),
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

    public function relationship(string $name, bool|Closure $condition = true): static
    {
        $static = $this->traitRelationship($name, $condition);

        return $static
            ->mutateRelationshipDataBeforeFillUsing(function () {
                $customForm = $this->getCachedExistingRecord();

                if (is_null($customForm)) {
                    $customForm = new CustomForm();
                }

                return $this->loadCustomFormEditorData($customForm);
            })
            ->saveRelationshipsUsing(function ($state, CustomFormEditor $component) {
                /**@var CustomForm $customForm */
                $customForm = $component->getCachedExistingRecord();
                $this->saveCustomFormEditorData($state, $customForm);
            });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columnSpanFull()
            ->columns(1)
            ->schema([
                Tabs::make()
                    ->extraAttributes(['class' => 'overflow-y-auto scroll-smooth'])
                    ->columnSpanFull()
                    ->tabs([
                        $this->getFormTab(),
                        $this->getRuleTab()
                    ]),
            ]);
    }


}
