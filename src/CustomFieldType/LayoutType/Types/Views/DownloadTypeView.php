<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class DownloadTypeView implements FieldTypeView
{
    use HasStaticMake;
    use CanMapFields;


    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $filePaths = $this->getOptionParameter($customField, 'files');

        if (count($filePaths) === 1) {
            $actions = $this->getSingleFilDownloadComponentAction($customField);
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction($customField);
        }

        $titelAsFileName = $this->getOptionParameter($customField, 'title_as_filename');
        $showTitle = $this->getOptionParameter($customField, 'show_label');

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                TextEntry::make($this->getIdentifyKey($customField) . '-title')
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . $this->getLabelName(
                                $customField
                            ) . '</span> <span>'
                        )
                    ),
                $actions,
            ];
        } else {
            $actions = [$actions];
        }

        //Toll Tip
        $helpText = $this->getOptionParameter($customField, 'helper_text');
        $helpText = empty($helpText) ? '' : $helpText;
        $helpText = new HtmlString(
            '</div> <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500" style="margin-top: -30px; ">' . $helpText . '</div><div>'
        );

        $actions[] = TextEntry::make('helper_text' . '-help_text') //ToDo Replace
        ->hiddenLabel()
            ->helperText($helpText);

        $group = Group::make($actions);
        $group->columnSpan($this->getOptionParameter($customField, 'column_span'));
        $group->columnStart($this->getOptionParameter($customField, 'new_line'));

        return $group;
    }


    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make($parameter['rendered'])
                ->columnStart(1)
                ->columnSpanFull()
                ->hidden();
        }

        $filePaths = $this->getOptionParameter($customFieldAnswer, 'files');

        if (count($filePaths) <= 1) {
            $actions = $this->getSingleFilDownloadComponentAction($customFieldAnswer->getCustomField());
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction($customFieldAnswer->getCustomField());
        }

        $titelAsFileName = $this->getOptionParameter($customFieldAnswer, 'title_as_filename');
        $showTitle = $this->getOptionParameter($customFieldAnswer, 'show_label');

        if (!$titelAsFileName && $showTitle) {
            $actions = [
                TextEntry::make($this->getIdentifyKey($customFieldAnswer) . '-title')
                    ->label(
                        new HtmlString(
                            '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . $this->getLabelName(
                                $customFieldAnswer
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
        $group->columnSpan($this->getOptionParameter($customFieldAnswer, 'column_span'));
        $group->columnStart($this->getOptionParameter($customFieldAnswer, 'new_line'));

        return $group;
    }

    private function getSingleFilDownloadComponentAction(EmbedCustomField $record): mixed
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

    private function getPathOfFileAbsolute(EmbedCustomField $record, mixed $path): string
    {
        $disk = $this->getTypeConfigAttribute($record, 'disk');
        $diskRoot = config('filesystems.disks.' . $disk . '.root');

        return $diskRoot . '/' . $path;
    }

    private function getMultipleFileDownloadComponentAction(EmbedCustomField $record): mixed
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
