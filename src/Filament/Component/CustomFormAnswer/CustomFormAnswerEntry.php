<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEmbeddedCustomForm;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;

class CustomFormAnswerEntry extends Entry implements CanEntangleWithSingularRelationships
{
    use HasEmbeddedCustomForm;

    protected string $view = 'filament-schemas::components.grid';
    protected CustomFormAnswer|Closure $answer;

    public function customFormAnswer(CustomFormAnswer|Closure $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCustomFormAnswer(): CustomFormAnswer
    {
        return $this->evaluate($this->answer);
    }

    public function getCustomForm(): CustomForm
    {
        return $this->getCustomFormAnswer()->customForm;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columns(1)
            ->schema(fn() => once(fn() => $this->getCustomFormSchema())) //ToDo simply
            ->customFormAnswer(fn($record) => $record) //ToDo Remove
            ->label('')
            ->autoViewMode();
    }

}
