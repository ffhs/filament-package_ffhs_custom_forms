<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Actions;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class EditCustomFormAction extends Action
{

    protected Model|Closure|null $record = null;
    protected string|null $customFormRelationShip = null;
    protected Model|null $parentRecord = null;




    protected function setUp(): void
    {
        $groupId = "customForm-" . uniqid();
        parent::setUp();
        $this->form(fn()=>[
            Group::make()
                ->relationship($this->getCustomFormRelationShip())
                ->schema(CustomFormEditForm::formSchema())
        ]);


        $this->record(function(){
            $relationShip =$this->getCustomFormRelationShip();
            return $this->parentRecord->$relationShip;
        });


        $this->modalWidth(MaxWidth::ScreenTwoExtraLarge);

        $this->closeModalByClickingAway(false);
        $this->slideOver();

        $this->fillForm(fn(CustomForm $record, array $data)=>[$groupId=>$record->toArray()]);

        $this->action(function (): void {
            //FromEdit Action
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

    public function getCustomFormRelationShip(): ?string {
        return is_null($this->customFormRelationShip)? $this->name:$this->customFormRelationShip;
    }

    public function setCustomFormRelationShip(?string $customFormRelationShip): static {
        $this->customFormRelationShip = $customFormRelationShip;
        return $this;
    }

    public function parentRecord(Model $record): static{
        $this->parentRecord = $record;
        return $this;
    }



}
