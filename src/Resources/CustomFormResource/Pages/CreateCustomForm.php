<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use App\Models\CustomField;
use App\Models\CustomFieldProductTerm;
use App\Models\ProductTerm;
use Cassandra\Uuid;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Rfc4122\UuidBuilder;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function form(Form $form): Form {
        return $form
            ->schema([

                Section::make("Form")
                    ->columns(3)
                    ->schema([
                        Select::make("custom_form_identifier")
                            ->live()
                            ->disabled(fn($get) => !is_null($get("custom_form_identifier")))
                            ->options(function (){
                                $keys = array_map(fn($config) => $config::identifier(),config("ffhs_custom_forms.forms"));
                                $values = array_map(fn($config) => $config::displayName(),config("ffhs_custom_forms.forms"));
                                return array_combine($keys,$values);
                            }),
                        Group::make()
                            ->hidden(fn($get) => is_null($get("custom_form_identifier")))
                            ->columnStart(1)
                            ->columns(3)
                            ->columnSpanFull()
                            ->schema([
                                Fieldset::make()
                                    ->columnStart(1)
                                    ->columnSpan(1)
                                    ->columns(1)
                                    ->schema($this->getFieldAddSchema()),

                                Group::make()
                                    ->columnSpan(2)
                                    ->schema([
                                        Repeater::make("custom_fields")
                                            ->relationship("customFields")
                                            ->addable(false)
                                            ->defaultItems(0)
                                            ->reorderable()
                                            ->itemLabel(fn($state)=>$state["name_de"])
                                            ->reorderableWithButtons()
                                            ->reorderableWithDragAndDrop(false) //(-_-) Return int if drag and drop. Why? (-_-)
                                            //->simple(TextInput with name and disabled) //(-_-) Dont work, or better say it works, but then it has no extraItemActions, and it creates a random new entry. Why? (-_-)
                                            //->expandAction(fn(Action $action)=> $action->icon('heroicon-m-pencil-square')->action(...)) //(-_-) Filament says no to edit the action function, so that can be collapsed all time and go over this Action (-_-)
                                            ->extraItemActions([
                                                Action::make('edit')
                                                    ->icon('heroicon-m-pencil-square')
                                                    ->form(fn($get)=>self::generalFieldForm($get))
                                                    ->action(function (array $arguments, Repeater $component): void {
                                                        dd("nice");
                                                    }),
                                            ])
,
                                    ]),
                        ]),
                    ]),

            ]);
    }

    private function getFieldAddSchema():array {
        return [
            //GeneralField
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            Select::make("add_general_field_id")
                ->label("")
                ->live()
                ->options(function ($get){
                    $formIdentifier = $get("custom_form_identifier");
                    $generalFields= GeneralFieldForm::query()
                        ->where("custom_form_identifier", $formIdentifier)
                        ->with("generalField")->get()
                        ->map(fn($generalFieldForm)=>$generalFieldForm->generalField);
                    return $generalFields->pluck("name_de","id"); //ToDo Translate
                }),
            Actions::make([
                Action::make("add_general_field")
                    ->label("Hinzufügen") //ToDo Translate
                    ->disabled(fn($get)=>is_null($get("add_general_field_id")))
                    ->form(fn($get)=>self::generalFieldForm($get))
                    ->action(function ($set,$get){
                        $fields = $get("custom_fields");
                        $fields[] = ["name_de" => GeneralField::query()->where("id",$get("add_general_field_id"))->first()->name_de];
                        $set("custom_fields",$fields);
                    }),
            ]),

            //Space
            Placeholder::make("")->content(""),
            //Placeholder::make("") ->content(""),

            //New CustomField
            Placeholder::make("")
                ->label("Spezifische Felder") //ToDo Translate
                ->content(""),

            Select::make("add_specify_field_type")
                ->label("")
                ->live()
                ->options(function ($get){
                    $formIdentifier = $get("custom_form_identifier");
                    $formConfiguration = self::getFormConfiguration($formIdentifier);
                    $types = $formConfiguration::formFieldTypes();

                    $keys = array_map(fn($type) => $type::getFieldIdentifier(),$types);
                    $values = array_map(fn($type) => (new $type)->getTranslatedName(), $types);
                    return array_combine($keys,$values);
                }),
            Actions::make([
                Action::make("add_custom_field")
                    ->label("Hinzufügen") //ToDo Translate
                    ->disabled(fn($get)=>is_null($get("add_specify_field_type")))
                    ->action(function ($set,$get){
                        $fields = $get("custom_fields");
                        $fields[] = ["name_de" => $get("add_specify_field_type")];
                        $set("custom_fields",$fields);
                    }),
            ])];


    }

    private static function generalFieldForm($globalGet):array {
        return [

            Group::make()
                ->columns(2)
                ->schema([
                    Tabs::make()
                        ->tabs(function (){
                            $tabs = [];
                            $tabs[] = self::createTab(null, true); //ToDo other Variations
                            return $tabs;
                        }),
                    Group::make([
                        Toggle::make("has_variations")
                            ->label("Gebunden an die Variation")//ToDo Translate
                            ->visible(fn()=> self::getFormConfiguration($globalGet("custom_form_identifier"))),
                    ])
                ])
        ];
    }


    private static function createTab (int|null $id, bool $isGeneral) :Tab{
        $tab = Tab::make(is_null($id)?"Template":"TO DO"); //ToDo
        $isTemplate = is_null($id);
        $tab->schema([
            Repeater::make("variation-".$id)
                ->deletable(false)
                ->cloneable(true)
                ->addable(false)
                ->defaultItems(1)
                ->minItems(1)
                ->label("")
                ->reorderable(false)
                ->live()
                ->schema([
                    //self::getCustomFieldTermsRepeaterSchema($isGeneral, GeneralField::allCached(), $isTermDisabled)->disabled($isTermDisabled)
                ])
               /* ->mutateRelationshipDataBeforeCreateUsing(function(array $data,$get,CustomField $record) use (
                    $isTermDisabled, $termId, $isGeneral) {
                    // Add Product Term ID
                    $data =array_merge($data,["product_term_id" => is_null($termId)? null:
                        ProductTerm::query()->where("term_id", "=", $termId)->where("product_id","=",$record->product()->first()->id)->first()->id]);// ["product_term_id" => is_null($termId)?null:$record::query()->where("term_id", "=", $termId)->where("product_id","=",$record->product()->first()->id)->first()->id]

                    // Disabled active if they new to a disabled term
                    if($isTermDisabled)$data["is_active"] = false;


                    $type= self::getCustomFieldType($isGeneral,$get,"");
                    return is_null($type)?$data:$type->prepareOptionDataBeforeCreate($data);
                })
                ->mutateRelationshipDataBeforeFillUsing(function (array $data,$get) use ($isGeneral) {
                    //Prepare field_options  that they can be filled
                    $type = self::getCustomFieldType($isGeneral,$get,"");
                    return is_null($type)? $data:$type->prepareOptionDataBeforeFill($data);
                })
                ->mutateRelationshipDataBeforeSaveUsing(function ($data,$get) use ($isGeneral, $termId) {
                    //Prepare field_options  that they can be save
                    $type= self::getCustomFieldType($isGeneral,$get,"");
                    return is_null($type)?$data:$type->prepareOptionDataBeforeSave($data);
                })

                ->relationship('customFieldProductTerms', fn(Builder $query) => is_null($termId)?
                    $query->whereNull("product_term_id"):
                    $query->leftJoin("product_term", "product_term.id", "custom_field_product_term.product_term_id")
                        ->where("term_id", $termId)
                        ->select("custom_field_product_term.*") //< = Imported
                )
                //Create new Recorde if a new ProductTerm add
                ->hidden(function (array|null $state, Repeater $component,$set) use ($isTermDisabled, $termId) {
                    if (!empty($state)) return;
                    $newRecord = [uniqid() => ['is_active' => !$isTermDisabled, 'required' => !$isTermDisabled, 'field_options'=>['options'=>[]]]];
                    $set($component->getName(), $newRecord);
                })*/
                ->cloneAction(
                    fn(Action $action) => $action
                        ->color(fn() => $isTemplate || $isTermDisabled? Color::Zinc: Color::Orange)
                        ->disabled($isTemplate || $isTermDisabled)
                        ->label("template") //ToDo
                        ->action(function ($set,$get) use ($isGeneral, $id) {
                            $template = array_values($get("customFieldTerms-"))[0];
                            $recordName = array_keys($get("customFieldTerms-".$id))[0];
                            $setPrefix = "customFieldTerms-".$id.".".$recordName.".";

                            $set($setPrefix.'required', $template['required']);
                            $set($setPrefix.'is_active', $template['is_active']);

                            //FieldOptions clone
                            $type = self::getCustomFieldType($isGeneral,$get,"");
                            $clonedFieldOptions = $type->prepareCloneOptions($template['field_options'],$isGeneral);
                            $set($setPrefix.'field_options', $clonedFieldOptions);
                        })
                )
        ]);

        return $tab;
    }

    private static function getFormConfiguration($formIdentifier): string{
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $formIdentifier)->first();
    }

}
