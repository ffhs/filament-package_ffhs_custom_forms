<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\HtmlString;

class FileUploadView implements FieldTypeView
{
    public static function getFormComponent(CustomFieldType $type, CustomField $record,
        array $parameter = []): Component {

        $saveFileNames = FormMapper::getOptionParameter($record,"preserve_filenames");

        $fileUpload = FileUpload::make(FormMapper::getIdentifyKey($record) . ($saveFileNames? ".files":""))
            ->label(FormMapper::getLabelName($record))
            ->helperText(FormMapper::getToolTips($record))
            ->columnSpan(FormMapper::getOptionParameter($record, "column_span"))
            ->inlineLabel(FormMapper::getOptionParameter($record, "in_line_label"))
            ->columnStart(FormMapper::getOptionParameter($record, "new_line_option"))
            ->multiple(FormMapper::getOptionParameter($record,"multiple"));
            //->downloadable(FormMapper::getOptionParameter($record,"downloadable"));


        if(FormMapper::getOptionParameter($record,"image")){
            $fileUpload = $fileUpload
                ->previewable(FormMapper::getOptionParameter($record,"show_images"))
                ->downloadable(FormMapper::getOptionParameter($record,"downloadable"))
                ->directory(FormMapper::getTypeConfigAttribute($record, "images.save_path"))
                ->disk(FormMapper::getTypeConfigAttribute($record, "images.disk"))
                ->image();
        }else{
            $fileUpload = $fileUpload
                ->directory(FormMapper::getTypeConfigAttribute($record, "files.save_path"))
                ->disk(FormMapper::getTypeConfigAttribute($record, "files.disk"))
                ->previewable(false);
        }

        if($saveFileNames)
            $fileUpload = $fileUpload->storeFileNamesIn(FormMapper::getIdentifyKey($record). '.file_names');


        return $fileUpload;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): \Filament\Infolists\Components\Component {

        $answer = FormMapper::getAnswer($record);

        if(is_null($answer) || !isset($answer['file_names'])) {
            $names = [];
            $files = [];
        }
        else if(FormMapper::getOptionParameter($record, "preserve_filenames")){
            $names = $answer['file_names'];
            $files= array_values($answer['files']);
        }else{
            $names = $answer;
            $files= array_values($answer);
        }


        //disk
        $image = FormMapper::getOptionParameter($record,"image");
        if($image) $disk = FormMapper::getTypeConfigAttribute($record, "images.disk");
        else $disk = FormMapper::getTypeConfigAttribute($record, "files.disk");
        $diskRoot = config('filesystems.disks.'.$disk.'.root');


        if(FormMapper::getOptionParameter($record, "image") && FormMapper::getOptionParameter($record, "show_images_in_view"))
            return self::getInfolistImageComponent($files, $diskRoot, $record, $names);

        return self::getInfoListFiles($files, $diskRoot, $record, $names);
    }


    private static function getInfolistImageComponent(mixed $files, mixed $diskRoot, CustomFieldAnswer $record, mixed $names): Group {
        $groups = [];
        foreach ($files as $path){
            $groups[] = Fieldset::make($names[$path])
                ->schema([
                    ImageEntry::make($path)
                        ->disk(FormMapper::getTypeConfigAttribute($record,"images.disk"))
                        ->label("")
                        ->state($path)
                        ->size(175),
                    Actions::make([
                        Action::make("download-".$path)
                            ->label("Download")
                            ->icon('tabler-download')
                            ->link()
                            ->action(fn() => response()->download($diskRoot."/".$path, $names[$path]))
                    ])->alignment(Alignment::Center)->visible(FormMapper::getOptionParameter($record,"downloadable"))
                ])
                ->columnSpan(1)
                ->columns(1)
                ->statePath(FormMapper::getIdentifyKey($record));
        }

        return Group::make([
            TextEntry::make(FormMapper::getIdentifyKey($record)."-title")
                ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FormMapper::getLabelName($record).'</span> <span>')),
            Grid::make()->schema($groups)->columns(5)
        ])->columnSpanFull()->columns(1);
    }




    private static function getInfoListFiles(mixed $files, mixed $diskRoot, CustomFieldAnswer $record, mixed $names): Group {
        $actions = [];

        $downloadable = FormMapper::getOptionParameter($record,"downloadable");

        foreach ($files as $path) {
            $absolutePath = $diskRoot."/".$path;
             $action = Action::make($path."-".FormMapper::getIdentifyKey($record)."-action")
                ->label($names[$path])
                ->disabled(!$downloadable)
                ->badge();

            if($downloadable){
                $action = $action
                    ->action(fn() => response()->download($absolutePath, $names[$path]))
                    ->icon('tabler-download');
            }
            $actions[] = $action;
        }

        return Group::make([
            TextEntry::make(FormMapper::getIdentifyKey($record)."-title")
                ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FormMapper::getLabelName($record).'</span> <span>')),
            Actions::make($actions)
        ])->columnSpanFull();
    }
}
