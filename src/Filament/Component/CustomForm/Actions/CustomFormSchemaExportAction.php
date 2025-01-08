<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomFormSchemaExportAction extends Action
{
    public function callExportAction(CustomForm $record): StreamedResponse
    {
        $exporter = FormSchemaExporter::make();
        $exportData = $exporter->export($record);
        $exportJson  = json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $type = $record->is_template ? 'Template' : 'Formular';
        $fileName =   $record->short_title . ' - ' .$type .' '. date('Y-m-d H:i') .'.json';


        Notification::make()
            ->title($type .' wurde erfolgreich exportiert')//ToDo Translate
            ->success()
            ->send();

        return response()->stream(function () use ($exportJson) {
            echo $exportJson; // Output the JSON data
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

    }

    public static function make(?string $name = 'export_custom_form'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->action($this->callExportAction(...));
        $this->label('Export Formular'); //ToDo Translate
    }
}
