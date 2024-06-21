<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormEditorValidation\FormEditorValidation;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;

class CustomFormEditor extends Component {

    protected string $view = 'filament-forms::components.group';

    public static function make(): static {
        $static = app(static::class);
        $static->configure();

        return $static;
    }





    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->columnSpanFull();
        $this->columns(3);

        $this->schema(fn(CustomForm $record) => [


            EditCustomForm::make("custom_fields")
                ->columnSpanFull(),
            #->live()->afterStateUpdated(fn($state, $old)=> dd($state,$old)),


        ]);


        return;
        $this->schema(fn(CustomForm $record) => [
            /**
             * List of custom fields and with icons
             * Dropdown with general fields
             * Dropdown with templates
             */
            Fieldset::make()
                ->columnStart(1)
                ->columnSpan(1)
                ->columns(1)
                ->schema(function() use ($record) {
                    return
                        collect($record->getFormConfiguration()::editorFieldAdder())
                            ->map(fn(string $class) => $class::make($record))
                            ->toArray();
                }),

            /**
             * Shows tree of the current form
             */
            EditorCustomFieldList::make($record)
                ->columnSpan(2)
                ->saveRelationshipsUsing(fn($component, $state) => CustomFormEditorSaveHelper::saveCustomFields($component, $record,
                    $state))
                ->rules([
                    fn(CustomForm $record) => function(string $attribute, $value, Closure $fail) use ($record) {
                        $formConfiguration = $record->getFormConfiguration();
                        foreach ($formConfiguration::editorValidations($record) as $editorValidationClass) {
                            $editorValidation = new $editorValidationClass();
                            /**@var FormEditorValidation $editorValidation ; */

                            $editorValidation->repeaterValidation($record, $fail, $value, $attribute);
                        }
                    }
                ]),
        ]);
    }
}
