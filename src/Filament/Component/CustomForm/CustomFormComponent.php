<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;

class CustomFormComponent extends Component
{


    protected string $view = 'filament-forms::components.group';
    protected string|Closure $viewMode;
    protected bool|Closure $isAutoSave;

    final public function __construct(string|Closure $viewMode = "default")
    {
        $this->viewMode = $viewMode;
        $this->isAutoSave = false;
    }

    public static function make(string|Closure $viewMode = "default"): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode,
        ]);
        $static->configure();

        return $static;
    }

    public function getViewMode(): string|Closure
    {
        return $this->evaluate($this->viewMode);
    }

    public function autoViewMode(bool|Closure $autoViewMode = true): static
    {
        if (!$this->evaluate($autoViewMode)) {
            return $this;
        }
        $this->viewMode = function (CustomFormAnswer $record) {
            $form = CustomForm::cached($record->custom_form_id);
            if ($record->customFieldAnswers->count() === 0) {
                return $form->getFormConfiguration()::displayCreateMode();
            } else {
                $form = CustomForm::cached($record->custom_form_id);
                return $form->getFormConfiguration()::displayEditMode();
            }
        };
        return $this;
    }

    public function getIsAutoSave(): bool
    {
        return $this->evaluate($this->isAutoSave);
    }

    public function autoSave(bool|Closure $isAutoSave = true): static
    {
        $this->isAutoSave = $isAutoSave;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->label("");
        $this->schema(fn(
            CustomFormAnswer $record,
            CustomFormComponent $component
        ) => CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id),
            $component->getViewMode())
        );
        $this->columns(1);

        //SetUp Auto Update
        $this->afterStateUpdated(function (CustomFormComponent $component, array $state, ?CustomFormAnswer $record) {
            if (!$component->getIsAutoSave()) {
                return;
            }
            CustomForms::save($record, $component->getLivewire()->getForm('form'));
        });

    }


}
