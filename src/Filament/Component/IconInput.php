<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Livewire;

class IconInput extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;
    protected Closure $modifyTextInput;
    protected Closure $modifyAction;
    protected string|Closure|Htmlable|null $label;



    protected string $view = 'filament-forms::components.group';

    public static function make(string $id): static
    {
        $static = app(static::class, [
            'id'=>$id,
            'label' => $id
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

        $this->schema(fn(IconInput $component,$set)=>[

            $component->modifyTextInput(TextInput::make($this->id)
                ->label(fn()=>$this->getLabel())
                ->live()
                ->prefixIcon(function($state) {
                    $icons = config("ffhs_custom_forms.icons");
                    $icons = collect($icons)->flatten(1);
                    if($icons->contains($state)) return $state ;
                    else return "";
                })),
            Group::make()
                ->columnSpan(1)
                ->schema([
                    Placeholder::make("")
                        ->content("")
                        ->hidden(fn()=>empty($component->getLabel())),
                    Actions::make([
                        $component->modifyAction(Actions\Action::make($component->id)
                            ->modalCancelAction(Action::make("hidden_canceled")->hidden())
                            ->modalSubmitAction(Action::make("hidden_submit")->hidden())
                            ->label("Icon Auswählen") //ToDo  Translate
                            ->modalWidth("7xl")
                            ->form(fn(Form $form)=>[
                                Group::make()
                                    ->columnSpanFull()
                                    ->columns(4)
                                    ->schema(function() use ($form,$set,$component) {
                                        $iconComponents=[];
                                        $iconSets = config("ffhs_custom_forms.icons");
                                        foreach ($iconSets as $iconSet){
                                            $actions = [];
                                            foreach ($iconSet as $icon){
                                                $actions[]= Actions\Action::make($icon."-". $this->id)
                                                    ->action(fn()=>$set($component->id, $icon))
                                                    ->size(ActionSize::ExtraLarge)
                                                    ->iconButton()
                                                    ->icon($icon)
                                                    ->outlined();
                                            }
                                            $iconComponents[] = Actions::make($actions);
                                        }
                                        return $iconComponents;
                                    })
                            ])
                        )
                    ])->columnSpan(1)
            ]),
        ]);
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

    public function getLabel(): Htmlable|string|null {
        return $this->evaluate($this->label);
    }

    public function label(Htmlable|Closure|string|null $label): static {
        $this->label = $label;
        return $this;
    }








}
