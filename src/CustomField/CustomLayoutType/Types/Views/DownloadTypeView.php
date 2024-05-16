<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class DownloadTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {

        $filePaths = FormMapper::getOptionParameter($record,"files");

        if(sizeof($filePaths) <= 1) $actions = self::getSingleFilDownloadComponentAction($record);
        else $actions = self::getMultipleFileDownloadComponentAction($record);

        $titelAsFileName = FormMapper::getOptionParameter($record, "title_as_filename") ;
        $showTitle = FormMapper::getOptionParameter($record, "show_title") ;

        if(!$titelAsFileName && $showTitle){
            $actions = [
                Placeholder::make(FormMapper::getIdentifyKey($record)."-title")
                    ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FormMapper::getLabelName($record).'</span> <span>')),
                $actions,
            ];
        }
        else  $actions = [$actions];

        //Toll Tip
        $actions[] =Placeholder::make(FormMapper::getToolTips($record)."-help_text")
            ->label("")
            ->helperText(new HtmlString('</div> <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500" style="margin-top: -30px; ">'. FormMapper::getToolTips($record). '</div><div>'));

        $group = Group::make($actions);


        $group->columnSpan(FormMapper::getOptionParameter($record, 'column_span'));
        $group->columnStart(FormMapper::getOptionParameter($record,"new_line_option"));

        return $group;
    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component{

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return TextEntry::make(FormMapper::getIdentifyKey($record))->label("")->state("");

        return TextEntry::make("w");
    }

    private static function getSingleFilDownloadComponentAction(CustomField $record): Actions {
        $paths = FormMapper::getOptionParameter($record, "files");
        $path = array_values($paths)[0];
        $fileName =   FormMapper::getOptionParameter($record, "file_names")[$path];
        $pathAbsolute = storage_path('app') . $path;

        $titelAsFileName = FormMapper::getOptionParameter($record, "title_as_filename") ;
        $label = $titelAsFileName ? FormMapper::getLabelName($record): $fileName;

        $action = Actions\Action::make(FormMapper::getIdentifyKey($record))
            ->action(fn()=>response()->download($pathAbsolute, $fileName))
            ->tooltip(FormMapper::getToolTips($record))
            ->icon('tabler-download')
            ->label($label);

        if (FormMapper::getOptionParameter($record, 'show_as_link')) $action = $action->link();

        $actions = Actions::make([$action]);

        return $actions;
    }

    private static function getMultipleFileDownloadComponentAction(CustomField $record): Actions {
        $filePaths = FormMapper::getOptionParameter($record,"files");
        $fileNames = FormMapper::getOptionParameter($record,"file_names");

        $showAsLink = FormMapper::getOptionParameter($record, 'show_as_link');

        $actions = [];

        foreach ($filePaths as $path){
            $name = $fileNames[$path];
            $pathAbsolute = storage_path('app') . $path;

            $action = Actions\Action::make(FormMapper::getIdentifyKey($record))
                ->action(fn()=>response()->download($pathAbsolute, $name))
                ->tooltip(FormMapper::getToolTips($record))
                ->icon('tabler-download')
                ->label($name);

            if ($showAsLink) $action = $action->link();
            $actions[] = $action;
        }
        return Actions::make($actions);
    }


}
