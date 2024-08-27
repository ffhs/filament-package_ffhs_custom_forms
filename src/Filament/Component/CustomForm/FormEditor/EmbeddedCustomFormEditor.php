<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;

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
        $this->mutateRelationshipDataBeforeFillUsing(function (array $data) {
            $form = CustomForm::cached($data["id"]);
            return EditCustomFormLoadHelper::load($form);
        });

    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->columns(1);
        $this->schema(fn(EmbeddedCustomFormEditor $component) =>
            [CustomFormEditor::make()->label($component->getLabel())]
        );
    }

}
