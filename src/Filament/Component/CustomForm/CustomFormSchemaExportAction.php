<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\FormSchemaExporter;
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

        $fileName =   $record->short_title . " - Formular " . date('Y-m-d H:i') .".json";

        Notification::make()
            ->title('Formular wurde erfolgreich exportiert')//ToDo Translate
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
        $this->label("Export Formular"); //ToDo Translate
    }


}
