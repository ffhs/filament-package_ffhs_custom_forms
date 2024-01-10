<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Actions;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
/**
 * the name has to be the field of the id of the CustomForm
 */
class EditCustomFormAction extends Action
{

    protected Model|Closure|null $record = null;



    protected function setUp(): void
    {
        parent::setUp();
        $this->form(CustomFormEditForm::formSchema());


        $this->record(fn($get)=> CustomForm::cached($get($this->name)));
        //Copied from Filament/EditAction clas
        $this->fillForm(function (HasActions $livewire,$record): array {

            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            return $data;
        });

        $this->modalWidth('7xl');
        $this->slideOver();

        $this->action(function (): void {
            $this->process(function (array $data, HasActions $livewire, Model $record) {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $translatableContentDriver->updateRecord($record, $data);
                } else {
                    $record->update($data);
                }
            });

            $this->success();
        });
    }

    //Copied from Filament/EditAction class
    public function process(?Closure $default, array $parameters = []): mixed
    {
        return $this->evaluate($this->using ?? $default, $parameters);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    public function getRecord(): ?Model
    {
        return $this->evaluate($this->record);
    }

    public function record(Model | Closure | null $record): static
    {
        $this->record = $record;

        return $this;
    }


}
