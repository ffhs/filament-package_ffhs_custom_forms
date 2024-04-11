<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Section;

class EmbeddedCustomFormEditor extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;


    protected string $view = 'filament-forms::components.group';

    public static function make(Closure|string $relationship): static
    {
        $static = app(static::class, [
            'relationship'=>$relationship,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(Closure|string $relationship)
    {
        $relationship = $this->evaluate($relationship);
        $this->relationship($relationship);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->schema(fn(EmbeddedCustomFormEditor $component)=>[
            Section::make($component->getLabel())->schema(CustomFormEditForm::formSchema())->columns(3)
        ]);
        $this->columns(1);
    }

}
