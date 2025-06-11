<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomFormSchemaExportAction extends Action
{
    private \Closure|CustomForm $customForm;

    public static function make(?string $name = 'export_custom_form'): static
    {
        return parent::make($name);
    }

    public function callExportAction(): StreamedResponse
    {
        $record = $this->getCustomForm();
        $exporter = FormSchemaExporter::make();
        $exportData = $exporter->export($record);
        $exportJson = json_encode($exportData,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $type = $record->is_template ? 'Template' : 'Formular';
        $fileName = $record->short_title . ' - ' . $type . ' ' . date('Y-m-d H:i') . '.json';


        Notification::make()
            ->title($type . ' wurde erfolgreich exportiert')//ToDo Translate
            ->success()
            ->send();

        return response()->stream(function () use ($exportJson) {
            echo $exportJson; // Output the JSON data
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

    }

    public function getCustomForm(): CustomForm
    {
        return $this->evaluate($this->customForm);
    }

    public function customForm(CustomForm|\Closure $customForm): static
    {
        $this->customForm = $customForm;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->customForm(fn(CustomForm $record) => $record);
        $this->action($this->callExportAction(...));
        $this->label('Export Formular'); //ToDo Translate
    }
}
