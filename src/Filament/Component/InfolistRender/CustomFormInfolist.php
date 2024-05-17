<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\InfolistRender;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;

class CustomFormInfolist extends Component
{


    protected string $view = 'filament-forms::components.group';
    protected string|Closure $viewMode;
    protected bool|Closure $isAutoSave;

    public static function make(string|Closure $viewMode= "default"): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(string|Closure $viewMode = "default")
    {
        $this->viewMode= $viewMode;
        $this->isAutoSave=false;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->schema(fn(CustomFormAnswer $record, CustomFormInfolist $component)=>
            CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id),$component->getViewMode())
        );
        $this->columns(1);

        //SetUp Auto Update
        $this->afterStateUpdated(function (CustomFormInfolist $component, array $state,?CustomFormAnswer $record){
            if(!$component->getIsAutoSave()) return;
            CustomFormSaveHelper::save($record, $state);
        });

    }

    public function getViewMode(): string|Closure {
        return $this->evaluate($this->viewMode);
    }


    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (CustomFormAnswer $record){
            $form = CustomForm::cached($record->custom_form_id);
            if($record->customFieldAnswers->count() == 0) return $form->getFormConfiguration()::displayCreateMode();
            else{
                $form = CustomForm::cached($record->custom_form_id);
                return $form->getFormConfiguration()::displayEditMode();
            }
        };
        return $this;
    }

    public function getIsAutoSave():bool {
        return $this->evaluate($this->isAutoSave);
    }

    public function autoSave(bool|Closure $isAutoSave = true):static {
        $this->isAutoSave = $isAutoSave;
        return $this;
    }


}
