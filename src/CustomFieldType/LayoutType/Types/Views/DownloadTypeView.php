<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;
use Filament\Forms\Components\Actions as FormsActions;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Group as FormsGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Actions as InfolistsActions;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\Group as InfolistsGroup;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class DownloadTypeView implements FieldTypeView
{
    use HasStaticMake;
    use CanMapFields;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent {
        $filePaths = $this->getOptionParameter($record, 'files');

        if (sizeof($filePaths) === 1) {
            $actions = $this->getSingleFilDownloadComponentAction($record, FormsActions::class);
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction($record, FormsActions::class);
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
        $actions[] = Placeholder::make('helper_text' . '-help_text')
            ->label('')
            ->helperText(
                new HtmlString(
                    '</div> <div class="fi-fo-field-wrp-helper-text text-sm text-gray-500" style="margin-top: -30px; ">' . empty($helpText) ? "" : $helpText . '</div><div>'
                )
            );

        $group = FormsGroup::make($actions);
        $group->columnSpan($this->getOptionParameter($record, 'column_span'));
        $group->columnStart($this->getOptionParameter($record, 'new_line'));

        return $group;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return InfolistsGroup::make($parameter['rendered'])
                ->columnStart(1)
                ->columnSpanFull()
                ->hidden();
        }

        $filePaths = $this->getOptionParameter($record, 'files');

        if (sizeof($filePaths) <= 1) {
            $actions = $this->getSingleFilDownloadComponentAction(
                $record->customField,
                InfolistsActions::class
            );
        } else {
            $actions = $this->getMultipleFileDownloadComponentAction(
                $record->customField,
                InfolistsActions::class
            );
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
        $group = InfolistsGroup::make($actions);
        $group->columnSpan($this->getOptionParameter($record, 'column_span'));
        $group->columnStart($this->getOptionParameter($record, 'new_line'));

        return $group;
    }

    private function getSingleFilDownloadComponentAction(CustomField $record, string $actionClass): mixed
    {
        $paths = $this->getOptionParameter($record, 'files');
        $path = array_values($paths)[0];
        $fileName = $this->getOptionParameter($record, 'file_names')[$path];
        $pathAbsolute = $this->getPathOfFileAbsolute($record, $path);

        $titelAsFileName = $this->getOptionParameter($record, 'title_as_filename');
        $label = $titelAsFileName ? $this->getLabelName($record) : $fileName;

        $action = ($actionClass . '\Action')::make($this->getIdentifyKey($record))
            ->action(fn() => response()->download($pathAbsolute, $fileName))
            ->icon('tabler-download')
            ->label($label);

        if ($this->getOptionParameter($record, 'show_as_link')) {
            $action = $action->link();
        }

        return $actionClass::make([$action]);
    }

    private function getPathOfFileAbsolute(CustomField $record, mixed $path): string
    {
        $disk = $this->getTypeConfigAttribute($record, 'disk');
        $diskRoot = config('filesystems.disks.' . $disk . '.root');

        return $diskRoot . '/' . $path;
    }

    private function getMultipleFileDownloadComponentAction(CustomField $record, string $actionClass): mixed
    {
        $filePaths = $this->getOptionParameter($record, 'files');
        $fileNames = $this->getOptionParameter($record, 'file_names');
        $showAsLink = $this->getOptionParameter($record, 'show_as_link');
        $actions = [];

        foreach ($filePaths as $path) {
            $name = $fileNames[$path];
            $pathAbsolute = $this->getPathOfFileAbsolute($record, $path);

            $action = ($actionClass . '\Action')::make($this->getIdentifyKey($record) . '-' . $name)
                ->action(fn() => response()->download($pathAbsolute, $name))
                ->icon('tabler-download')
                ->label($name);

            if ($showAsLink) {
                $action = $action->link();
            }

            $actions[] = $action;
        }

        return $actionClass::make($actions);
    }
}
