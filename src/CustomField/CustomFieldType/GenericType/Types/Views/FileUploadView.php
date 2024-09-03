<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
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

    use HasDefaultViewComponent;

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component {

        $fileUpload = FileUpload::make(FieldMapper::getIdentifyKey($record) . ".files");
        return self::prepareFileUploadComponent($fileUpload,$record);
    }



    public static function prepareFileUploadComponent(FileUpload $component, $record): FileUpload {
        $component = static::modifyFormComponent($component, $record);
        /** @var FileUpload $component */
        $component
            ->multiple(fn($state) => FieldMapper::getOptionParameter($record,"multiple"))
            ->downloadable(FieldMapper::getOptionParameter($record,"downloadable")) ///ToDo URL storage/uploaded_files/01J6VFAEN4K94E7V02S90DKPKW.png
            ->acceptedFileTypes(FieldMapper::getOptionParameter($record, 'allowed_type'))
            ->appendFiles();


        if(FieldMapper::getOptionParameter($record,"image")){
            $component = $component
                ->previewable(FieldMapper::getOptionParameter($record,"show_images"))
                ->downloadable(FieldMapper::getOptionParameter($record,"downloadable"))
                ->disk(FieldMapper::getTypeConfigAttribute($record, "images.disk"))
                ->directory(FieldMapper::getTypeConfigAttribute($record, "images.save_path"))
                ->image();
        }else{
            $component = $component
                ->directory(FieldMapper::getTypeConfigAttribute($record, "files.save_path"))
                ->disk(FieldMapper::getTypeConfigAttribute($record, "files.disk"))
                ->previewable(false);
        }

        if(FieldMapper::getOptionParameter($record,"preserve_filenames"))
            $component = $component->storeFileNamesIn(FieldMapper::getIdentifyKey($record). '.file_names');

        if(FieldMapper::getOptionParameter($record,"grid_layout"))
            $component = $component->panelLayout('grid');
        return $component;
    }




    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {

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
            if(!is_array($names)) $names = [$path => $names];
            $urlPrefix = FieldMapper::getTypeConfigAttribute($record, "images.url_prefix");

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
                            ->action(fn() => response()->download($diskRoot."/".$path, $names[$path])),
                        Action::make($path . "-" . FieldMapper::getIdentifyKey($record) . "-action-view")
                            ->action(fn() => response()->redirectTo($urlPrefix . "/" . $path, $names[$path]))
                            ->icon('bi-folder-symlink')
                            ->label("Redirect")
                            ->link()
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

        $downloadable = FieldMapper::getOptionParameter($record,"downloadable");
        $openInNewTab = FieldMapper::getOptionParameter($record,"open_in_new_tab");

        $fileComponents = [];

        foreach ($files as $path) {
            if(!is_array($names)) $names = [$path => $names];

            $absolutePath = $diskRoot."/".$path;
            $actions = [];


            if($downloadable) $actions[]=
                Action::make($path."-".FieldMapper::getIdentifyKey($record)."-action-download")
                    ->action(fn() => response()->download($absolutePath, $names[$path]))
                    ->icon('tabler-download')
                    ->label("Download")
                    ->link()
                    ->iconButton();

            if($openInNewTab) {
                $urlPrefix = FieldMapper::getTypeConfigAttribute($record, "files.url_prefix");
                $actions[] =
                    Action::make($path . "-" . FieldMapper::getIdentifyKey($record) . "-action-view")
                        ->action(fn() => response()->redirectTo($urlPrefix . "/" . $path, $names[$path]))
                        ->icon('bi-folder-symlink')
                        ->label("Redirect")
                        ->link()
                        ->iconButton();
            }

            $fileComponents[] = Group::make([
                TextEntry::make("file_name" . $path)
                    ->label("")
                    ->state($names[$path])
                    ->columnSpan(2),
                Actions::make($actions)
            ])
                ->extraAttributes(['class' => 'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10
                dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary fi-ac-action fi-ac-badge-action', "style" => "--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);"])->columns(3)
                ->columnStart(1);
        }

        //fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary fi-ac-action fi-ac-badge-action

        return Group::make([
            TextEntry::make(FieldMapper::getIdentifyKey($record)."-title")
                ->label(new HtmlString('</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">'.FieldMapper::getLabelName($record).'</span> <span>')),
          Grid::make()->schema($fileComponents)->columns()
        ])->columnSpanFull();
    }

}
