<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
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
                                            array           $parameter = []): Component {


        $fileUpload = FileUpload::make(FieldMapper::getIdentifyKey($record) . ".files");
        return self::prepareFileUploadComponent($fileUpload,$record);
    }

    public static function prepareFileUploadComponent(FileUpload $component,$record): FileUpload {
        $component->label(FieldMapper::getLabelName($record))
            ->helperText(FieldMapper::getToolTips($record))
            ->columnSpan(FieldMapper::getOptionParameter($record, "column_span"))
            ->inlineLabel(FieldMapper::getOptionParameter($record, "in_line_label"))
            ->columnStart(FieldMapper::getOptionParameter($record, "new_line_option"))
            ->multiple(FieldMapper::getOptionParameter($record,"multiple"));
        //->downloadable(FormMapper::getOptionParameter($record,"downloadable"));


        if(FieldMapper::getOptionParameter($record,"image")){
            $component = $component
                ->previewable(FieldMapper::getOptionParameter($record,"show_images"))
                ->downloadable(FieldMapper::getOptionParameter($record,"downloadable"))
                ->directory(FieldMapper::getTypeConfigAttribute($record, "images.save_path"))
                ->disk(FieldMapper::getTypeConfigAttribute($record, "images.disk"))
                ->image();
        }else{
            $component = $component
                ->directory(FieldMapper::getTypeConfigAttribute($record, "files.save_path"))
                ->disk(FieldMapper::getTypeConfigAttribute($record, "files.disk"))
                ->previewable(false);
        }

        if(FieldMapper::getOptionParameter($record,"preserve_filenames"))
            $component = $component->storeFileNamesIn(FieldMapper::getIdentifyKey($record). '.file_names');

        return $component;
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): \Filament\Infolists\Components\Component {

        $answer = FieldMapper::getAnswer($record);

        if(is_null($answer) || !isset($answer['file_names'])) {
            $names = [];
            $files = [];
        }
        else if(FieldMapper::getOptionParameter($record, "preserve_filenames")){
            $names = $answer['file_names'];
            $files= array_values($answer['files']);
        }else{
            $names = $answer;
            $files= array_values($answer['files']);
        }

        //disk
        $image = FieldMapper::getOptionParameter($record,"image");
        if($image) $disk = FieldMapper::getTypeConfigAttribute($record, "images.disk");
        else $disk = FieldMapper::getTypeConfigAttribute($record, "files.disk");
        $diskRoot = config('filesystems.disks.'.$disk.'.root');


        if(FieldMapper::getOptionParameter($record, "image") && FieldMapper::getOptionParameter($record, "show_images_in_view"))
            return self::getInfolistImageComponent($files, $diskRoot, $record, $names);

        return self::getInfoListFiles($files, $diskRoot, $record, $names);
    }


    private static function getInfolistImageComponent(mixed $files, mixed $diskRoot, CustomFieldAnswer $record, mixed $names): Group {
        $groups = [];
        foreach ($files as $path){
            $groups[] = Fieldset::make($names[$path])
                ->schema([
                    ImageEntry::make($path)
                        ->disk(FieldMapper::getTypeConfigAttribute($record,"images.disk"))
                        ->label("")
                        ->state($path)
                        ->size(175),
                    Actions::make([
                        Action::make("download-".$path)
                            ->label("Download")
                            ->icon('tabler-download')
                            ->link()
                            ->action(fn() => response()->download($diskRoot."/".$path, $names[$path]))
                    ])->alignment(Alignment::Center)->visible(FieldMapper::getOptionParameter($record,"downloadable"))
                ])
                ->columnSpan(1)
                ->columns(1)
                ->statePath(FieldMapper::getIdentifyKey($record));
        }


        return Group::make([
            TextEntry::make(FieldMapper::getIdentifyKey($record)."-title")
                ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FieldMapper::getLabelName($record).'</span> <span>')),
            Grid::make()->schema($groups)->columns(5)
        ])->columnSpanFull()->columns(1);
    }


    private static function getInfoListFiles(mixed $files, mixed $diskRoot, CustomFieldAnswer $record, mixed $names): Group {
        $actions = [];

        $downloadable = FieldMapper::getOptionParameter($record,"downloadable");

        foreach ($files as $path) {
            $absolutePath = $diskRoot."/".$path;
             $action = Action::make($path."-".FieldMapper::getIdentifyKey($record)."-action")
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
            TextEntry::make(FieldMapper::getIdentifyKey($record)."-title")
                ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FieldMapper::getLabelName($record).'</span> <span>')),
            Actions::make($actions)
        ])->columnSpanFull();
    }
}
