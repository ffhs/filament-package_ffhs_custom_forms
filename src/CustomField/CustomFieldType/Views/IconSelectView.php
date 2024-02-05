<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\IconInput;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\ActionSize;

class IconSelectView implements FieldTypeView
{


    public static function getFormComponent(CustomFieldType $type, CustomFieldVariation $record,
        array $parameter = []): Component {
        return IconInput::make($type::getIdentifyKey($record))
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->columnSpan($type->getOptionParameter($record,"column_span"))
            ->modifyTextInputUsing(fn(TextInput $textInput)=> $textInput
                ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
                ->helperText($type::class::getToolTips($record))
                ->label($type::class::getLabelName($record))
            );

        /*Group::make()
            ->columns()
            ->schema([
                TextInput::make($type::getIdentifyKey($record))
                    ->inlineLabel($type->getOptionParameter($record,"in_line_label"))
                    ->helperText($type::class::getToolTips($record))
                    ->label($type::class::getLabelName($record))
                    ->prefixIcon(function($state) {
                        $icons = config("ffhs_custom_forms.icons");
                        if(in_array($state,$icons)) return$state;
                        else return "";
                    })
                    ->live(),
                Actions::make([
                    Actions\Action::make($type::getIdentifyKey($record) . "-select")
                    ->form([
                        Group::make()
                            ->columns(4)
                            ->schema(function() use ($record, $type) {
                                $iconComponents=[];
                                $icons = config("ffhs_custom_forms.icons");
                                foreach ($icons as $icon){
                                    $iconComponents[] = Actions::make([
                                        Actions\Action::make($icon."-". $type::getIdentifyKey($record))
                                            ->action(fn($set)=> $set($type::getIdentifyKey($record), $icon))
                                            ->size(ActionSize::ExtraLarge)
                                            ->iconButton()
                                            ->icon($icon)
                                            ->outlined()
                                    ]);
                                }


                                return $iconComponents;
                            })
                    ])
                ])
            ]);*/
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): IconEntry {
        return IconEntry::make($type::getIdentifyKey($record))
            ->label($type::class::getLabelName($record). ":")
            ->columnStart($type->getOptionParameter($record,"new_line_option"))
            ->state(is_null($record->answer)? false : $record->answer)
            ->inlineLabel()
            ->boolean();
    }

}
