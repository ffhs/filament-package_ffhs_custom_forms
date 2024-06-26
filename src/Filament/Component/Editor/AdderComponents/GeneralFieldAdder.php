<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class GeneralFieldAdder extends FormEditorFieldAdder
{

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.field-adder.general_adder';

    protected $usedGeneralFieldIds;

    protected function setUp(): void {
        parent::setUp();
        $this->live();
        $this->label(__("filament-package_ffhs_custom_forms::custom_forms.form.compiler.general_fields"));
    }


    public function getIcon($id): string {
        return GeneralField::cached($id)->icon;
    }

    public function getGeneralFieldSelectOptions() {

        $formIdentifier = data_get($this->getState(), "custom_form_identifier");

        $generalFieldForms = GeneralFieldForm::getFromFormIdentifier($formIdentifier);

        GeneralField::cachedMultiple('id', true, ...$generalFieldForms->pluck("general_field_id")->toArray());

        //Mark Required GeneralFields
        $generalFields = $generalFieldForms->map(function (GeneralFieldForm $generalFieldForm) {
            $generalField = $generalFieldForm->generalField;

            if ($generalFieldForm->is_required) {
                $generalField->name = "* ".$generalField->name;
                $generalField->name = "* ".$generalField->name;
            }
            return $generalField;
        });

        return $generalFields->pluck("name", "id"); //ToDo Translate
    }


    public function isGeneralDisabled($id):bool {
        if(empty($usedGeneralFieldIds))
            $usedGeneralFieldIds = collect($this->getState()['custom_fields']['data'])->pluck("general_field_id");

        return $usedGeneralFieldIds->contains($id);
    }

}
