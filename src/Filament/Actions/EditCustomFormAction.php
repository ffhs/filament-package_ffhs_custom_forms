<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Actions;

use App\Models\FormRelation;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

/**
 * the name has to be the field of the id of the CustomForm
 */
class EditCustomFormAction extends Action
{

    protected Model|Closure|null $record = null;
    protected int|null $customFormId = null;

    //ToDo THE SHIT DOESNT WORK

    protected function setUp(): void
    {
        parent::setUp();
        $this->form(CustomFormEditForm::formSchema());

        $this->record(fn($get)=> CustomForm::cached(is_null($this->customFormId)?$get($this->name): $this->customFormId));


        $this->modalWidth('7xl');

        $this->closeModalByClickingAway(false);
        $this->slideOver();

        $this->mountUsing(function (Form $form, $record,$livewire) {
            $form->model($record);

            //Copied from Filament/EditAction class
            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver())
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            else
                $data = $record->attributesToArray();

            $form->fill($data);
        });


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

    public function customFormId($recordId): static {
        $this->customFormId = $recordId;
        return $this;
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
