<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\EditCustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;

class IconInput extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;
    protected Closure $modifyTextInput;
    protected Closure $modifyAction;



    protected string $view = 'filament-forms::components.group';

    public static function make(string $id): static
    {
        $static = app(static::class, [
            'id'=>$id,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(string $id)
    {
        $this->id($id);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->modifyTextInput = fn($textInput)=>$textInput;
        $this->modifyAction = fn($action)=>$action;

        $this->label("");
        $this->columns();

        $textInput =  TextInput::make($this->id)
            ->label($this->id)
            ->live()
            ->prefixIcon(function($state) {
                $icons = config("ffhs_custom_forms.icons");
                if(in_array($state,$icons)) return$state;
                else return "";
            });
        $textInput = $this->modifyTextInput($textInput);

        $mainAction = Actions\Action::make($this->id)
            ->modalCancelAction(Action::make("hidden_canceled")->hidden())
            ->modalSubmitAction(Action::make("hidden_submit")->hidden())
            ->modalWidth("7xl")
            ->form(fn(Form $form)=>[
                Group::make()
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema(function() use ( $form) {
                        $iconComponents=[];
                        $iconSets = config("ffhs_custom_forms.icons");
                        foreach ($iconSets as $iconSet){
                            $actions = [];
                            foreach ($iconSet as $icon){
                                    $actions[]= Actions\Action::make($icon."-". $this->id)
                                        ->action(function($set,EditCustomForm $livewire) use ($icon) {
                                            $forms = $livewire->getCachedForms();
                                            $form = array_values($forms)[sizeof($forms)-1];
                                            $livewire->dispatchBrowserEvent('close-modal');
                                            $set($this->id, $icon);
                                            return redirect()->back()->withInput();
                                        })
                                        ->size(ActionSize::ExtraLarge)
                                        ->iconButton()
                                        ->icon($icon)
                                        ->outlined();
                            }
                            $iconComponents[] = Actions::make($actions);
                        }
                        return $iconComponents;
                    })
            ]);

        $mainAction = $this->modifyAction($mainAction);

        $this->schema(fn(IconInput $component)=>[$textInput,Actions::make([$mainAction])]);
    }

    public function modifyTextInputUsing(?Closure $modifyTextInput): static {
        $this->modifyTextInput = $modifyTextInput;
        return $this;
    }

    public function modifyActionUsing(?Closure $modifyAction): static {
        $this->modifyAction = $modifyAction;
        return $this;
    }



    public function modifyTextInput(TextInput $textInput): TextInput {
        return $this->evaluate($this->modifyTextInput, ["textInput"=>$textInput], [TextInput::class=> $textInput]);
    }

    public function modifyAction(Action $action): Action {
        return $this->evaluate($this->modifyAction, ["action"=>$action], [Action::class=> $action]);
    }






}
