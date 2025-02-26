<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class DownloadTypeView implements FieldTypeView
{

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        $filePaths = FieldMapper::getOptionParameter($record, "files");

        if (sizeof($filePaths) == 1) {
            $actions = self::getSingleFilDownloadComponentAction($record, Actions::class);
        } else $actions = self::getMultipleFileDownloadComponentAction($record, Actions::class);

        $titelAsFileName = FieldMapper::getOptionParameter($record, "title_as_filename");
        $showTitle = FieldMapper::getOptionParameter($record, "show_title");

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                Placeholder::make(FieldMapper::getIdentifyKey($record) . "-title")
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . FieldMapper::getLabelName(
                                $record
                            ) . '</span> <span>'
                        )
                    ),
                $actions,
            ];
        } else  $actions = [$actions];

        //Toll Tip
        $helpText = FieldMapper::getOptionParameter($record, "helper_text");
        $actions[] = Placeholder::make("helper_text" . "-help_text")
            ->label("")
            ->helperText(
                new HtmlString(
                    '</div> <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500" style="margin-top: -30px; ">' . empty($helpText) ? "" : $helpText . '</div><div>'
                )
            );

        $group = Group::make($actions);


        $group->columnSpan(FieldMapper::getOptionParameter($record, 'column_span'));
        $group->columnStart(FieldMapper::getOptionParameter($record, "new_line"));

        return $group;
    }

    private static function getSingleFilDownloadComponentAction(CustomField $record, string $actionClass): mixed
    {
        $paths = FieldMapper::getOptionParameter($record, "files");
        $path = array_values($paths)[0];
        $fileName = FieldMapper::getOptionParameter($record, "file_names")[$path];
        $pathAbsolute = self::getPathOfFileAbsolute($record, $path);

        $titelAsFileName = FieldMapper::getOptionParameter($record, "title_as_filename");
        $label = $titelAsFileName ? FieldMapper::getLabelName($record) : $fileName;

        $action = ($actionClass . '\Action')::make(FieldMapper::getIdentifyKey($record))
            ->action(fn() => response()->download($pathAbsolute, $fileName))
            ->icon('tabler-download')
            ->label($label);

        if (FieldMapper::getOptionParameter($record, 'show_as_link')) $action = $action->link();

        return $actionClass::make([$action]);
    }

    private static function getPathOfFileAbsolute(CustomField $record, mixed $path): string
    {
        $disk = FieldMapper::getTypeConfigAttribute($record, 'disk');
        $diskRoot = config('filesystems.disks.' . $disk . '.root');
        return $diskRoot . "/" . $path;
    }

    private static function getMultipleFileDownloadComponentAction(CustomField $record, string $actionClass): mixed
    {
        $filePaths = FieldMapper::getOptionParameter($record, "files");
        $fileNames = FieldMapper::getOptionParameter($record, "file_names");

        $showAsLink = FieldMapper::getOptionParameter($record, 'show_as_link');

        $actions = [];

        foreach ($filePaths as $path) {
            $name = $fileNames[$path];
            $pathAbsolute = self::getPathOfFileAbsolute($record, $path);

            $action = ($actionClass . '\Action')::make(FieldMapper::getIdentifyKey($record) . "-" . $name)
                ->action(fn() => response()->download($pathAbsolute, $name))
                ->icon('tabler-download')
                ->label($name);

            if ($showAsLink) $action = $action->link();
            $actions[] = $action;
        }
        return $actionClass::make($actions);
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return \Filament\Infolists\Components\Group::make($parameter["rendered"])->columnStart(1)->columnSpanFull(
            )->hidden();
        }

        $filePaths = FieldMapper::getOptionParameter($record, "files");

        if (sizeof($filePaths) <= 1) {
            $actions = self::getSingleFilDownloadComponentAction(
                $record->customField,
                \Filament\Infolists\Components\Actions::class
            );
        } else {
            $actions = self::getMultipleFileDownloadComponentAction(
                $record->customField,
                \Filament\Infolists\Components\Actions::class
            );
        }

        $titelAsFileName = FieldMapper::getOptionParameter($record, "title_as_filename");
        $showTitle = FieldMapper::getOptionParameter($record, "show_title");

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                TextEntry::make(FieldMapper::getIdentifyKey($record) . "-title")
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . FieldMapper::getLabelName(
                                $record
                            ) . '</span> <span>'
                        )
                    ),
                $actions,
            ];
        } else  $actions = [$actions];

        //Toll Tip
        $group = \Filament\Infolists\Components\Group::make($actions);

        $group->columnSpan(FieldMapper::getOptionParameter($record, 'column_span'));
        $group->columnStart(FieldMapper::getOptionParameter($record, "new_line"));

        return $group;
    }


}
