<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Closure;
use Ffhs\FfhsUtils\Filament\Components\RuleEditor\RuleEditor;
use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FormEditorSideComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DefaultFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\EditFieldsGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;

class CustomFormEditor extends Field implements CanEntangleWithSingularRelationships
{
    use HasFormConfiguration;
    use HasFormGroupName;
    use CanLoadCustomFormEditorData;
    use CanSaveCustomFormEditorData;
    use EntanglesStateWithSingularRelationship {
        EntanglesStateWithSingularRelationship::relationship as parentRelationship;
    }

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.form-editor.index';

    public function relationship(
        string $name,
        bool|Closure $condition = true,
        string|Closure|null $relatedModel = null
    ): static {
        $this->parentRelationship($name, $condition, $relatedModel);

        $this->saveRelationshipsBeforeChildrenUsing(function (Component|CanEntangleWithSingularRelationships $component
        ): void {
            $state = $component->getState();
            $record = $component->getCachedExistingRecord();

            $this->saveCustomFormEditorData($state, $record);
            $this->clearCachedExistingRecord();
            $this->fillFromRelationship();
        });

        $this->formConfiguration(function (): CustomFormConfiguration {
            /**@var null|CustomForm $customForm */
            $customForm = $this->getCachedExistingRecord();
            /**@phpstan-ignore-next-line */
            $formConfiguration = $customForm?->getFormConfiguration();
            return $formConfiguration ?? DefaultFormConfiguration::make();
        });

        return $this;
    }

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

    protected function getStateFromRelatedRecord(CustomForm $record): array
    {
        return $this->loadCustomFormEditorData($record);
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
                        ->columns(3)
                        ->schema([
                            RuleEditor::make('rules')
                                ->triggers(fn() => $this->getFormConfiguration()->getRuleTriggerTypes())
                                ->events(fn() => $this->getFormConfiguration()->getRuleEventTypes())
                                ->group(fn() => $this->getGroupName() . '-rules')
                                ->columnSpan(2)
                        ])
                ])
        ]);
    }
}
