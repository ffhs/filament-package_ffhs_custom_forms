<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class DownloadTypeView implements FieldTypeView
{
    use HasStaticMake;
    use CanMapFields;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $filePaths = $this->getOptionParameter($record, 'files');

        if (count($filePaths) === 1) {
            $actions = $this->getSingleFilDownloadComponentAction($record);
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction($record);
        }

        $titelAsFileName = $this->getOptionParameter($record, 'title_as_filename');
        $showTitle = $this->getOptionParameter($record, 'show_label');

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                Placeholder::make($this->getIdentifyKey($record) . '-title')
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . $this->getLabelName(
                                $record
                            ) . '</span> <span>'
                        )
                    ),
                $actions,
            ];
        } else {
            $actions = [$actions];
        }

        //Toll Tip
        $helpText = $this->getOptionParameter($record, 'helper_text');
        $actions[] = Placeholder::make('helper_text' . '-help_text') //ToDo Replace
        ->label('')
            ->helperText(
                new HtmlString(
                    '</div> <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500" style="margin-top: -30px; ">' . empty($helpText) ? "" : $helpText . '</div><div>'
                )
            );

        $group = Group::make($actions);
        $group->columnSpan($this->getOptionParameter($record, 'column_span'));
        $group->columnStart($this->getOptionParameter($record, 'new_line'));

        return $group;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return Group::make($parameter['rendered'])
                ->columnStart(1)
                ->columnSpanFull()
                ->hidden();
        }

        $filePaths = $this->getOptionParameter($record, 'files');

        if (count($filePaths) <= 1) {
            $actions = $this->getSingleFilDownloadComponentAction($record->customField);
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction($record->customField);
        }

        $titelAsFileName = $this->getOptionParameter($record, 'title_as_filename');
        $showTitle = $this->getOptionParameter($record, 'show_label');

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                TextEntry::make($this->getIdentifyKey($record) . '-title')
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . $this->getLabelName(
                                $record
                            ) . '</span> <span>'
                        )
                    ),
                $actions,
            ];
        } else {
            $actions = [$actions];
        }

        //Toll Tip
        $group = Group::make($actions);
        $group->columnSpan($this->getOptionParameter($record, 'column_span'));
        $group->columnStart($this->getOptionParameter($record, 'new_line'));

        return $group;
    }

    private function getSingleFilDownloadComponentAction(CustomField $record): mixed
    {
        $paths = $this->getOptionParameter($record, 'files');
        $path = array_values($paths)[0];
        $fileName = $this->getOptionParameter($record, 'file_names')[$path];
        $pathAbsolute = $this->getPathOfFileAbsolute($record, $path);

        $titelAsFileName = $this->getOptionParameter($record, 'title_as_filename');
        $label = $titelAsFileName ? $this->getLabelName($record) : $fileName;

        $action = Action::make($this->getIdentifyKey($record))
            ->action(fn() => response()->download($pathAbsolute, $fileName))
            ->icon('tabler-download')
            ->label($label);

        if ($this->getOptionParameter($record, 'show_as_link')) {
            $action = $action->link();
        }

        return $action;
    }

    private function getPathOfFileAbsolute(CustomField $record, mixed $path): string
    {
        $disk = $this->getTypeConfigAttribute($record, 'disk');
        $diskRoot = config('filesystems.disks.' . $disk . '.root');

        return $diskRoot . '/' . $path;
    }

    private function getMultipleFileDownloadComponentAction(CustomField $record): mixed
    {
        $filePaths = $this->getOptionParameter($record, 'files');
        $fileNames = $this->getOptionParameter($record, 'file_names');
        $showAsLink = $this->getOptionParameter($record, 'show_as_link');
        $actions = [];

        foreach ($filePaths as $path) {
            $name = $fileNames[$path];
            $pathAbsolute = $this->getPathOfFileAbsolute($record, $path);

            $action = Action::make($this->getIdentifyKey($record) . '-' . $name)
                ->action(fn() => response()->download($pathAbsolute, $name))
                ->icon('tabler-download')
                ->label($name);

            if ($showAsLink) {
                $action = $action->link();
            }

            $actions[] = $action;
        }

        return Actions::make($actions);
    }
}
