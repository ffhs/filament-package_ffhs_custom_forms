<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FormEditorSideComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\EditFieldsGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;

class FormEditor extends Field
{
    use HasFormConfiguration;
    use HasFormGroupName;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.form-editor.index';

    public function getSideComponents(): array
    {
        $components = [];
        $classes = $this->getFormConfiguration()->getSideComponentModifiers();
        $formConfiguration = $this->getFormConfiguration();

        foreach ($classes as $class) {
            /**@var FormEditorSideComponent $class */
            $components[] = $class::getSiteComponent($formConfiguration);
        }

        return $components;
    }

    public function getDefaultStateCasts(): array
    {
        $casts = parent::getDefaultStateCasts();
        $casts[] = new CustomFieldStateCast();

        return $casts;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull();
        $this->columns(1);
        $this->schema([
            Tabs::make()
                ->tabs([
                    Tabs\Tab::make(CustomForm::__('label.single'))
                        ->icon('carbon-data-format')
                        ->columns(5)
                        ->schema([
                            Fieldset::make()
                                ->extraAttributes([])
                                ->schema($this->getSideComponents(...))
                                ->extraAttributes(['class' => 'self-stretch'])
                                ->columnSpan(1)
                                ->columns(1),
                            EditFieldsGroup::make('custom_fields')
                                ->columns(fn() => $this->getFormConfiguration()->getColumns())
                                ->formConfiguration($this->getFormConfiguration(...))
                                ->extraAttributes(['class' => 'self-stretch'])
                                ->columnSpan(4),
                        ]),

                    Tabs\Tab::make(Rule::__('label.single'))
                        ->icon('carbon-rule')
                        ->columns(1)
                        ->schema([
                            RuleEditor::make('rules')
                                ->triggers(fn() => $this->getFormConfiguration()->getRuleTriggerTypes() ?? [])
                                ->events(fn() => $this->getFormConfiguration()->getRuleEventTypes() ?? [])
                                ->group(fn() => $this->getGroupName() . '-rules')
                                ->columnSpanFull()
                        ])
                ])
        ]);
    }
}
