<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions;

use Closure;
use Error;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormImportException;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use JsonException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CustomFormSchemaImportAction extends Action
{
    private Closure|CustomForm|null $existingForm = null;

    public static function make(?string $name = 'import_custom_form'): static
    {
        return parent::make($name);
    }

    public function existingForm(Closure|CustomForm|null $existingForm): static
    {
        $this->existingForm = $existingForm;

        return $this;
    }

    /**
     * @throws FormImportException|JsonException
     */
    public function callImportAction($data): void
    {
        $importer = FormSchemaImporter::make();

        $file = $data['form_file'];
        $formData = $this->getUploadedInfos($data['form_file']);
        $type = '';

        $shouldImportRules = $data['should_import_rules'] ?? true;

        $generalFieldMapRaw = $data['general_field_map'];
        $templateMapRaw = $data['template_map'];

        if (!$shouldImportRules) {
            unset($formData['rules']);
        }

        $templateMap = [];

        foreach ($templateMapRaw as $templateMapped) {
            $templateMap[$templateMapped['template_identifier']] = $templateMapped['template_id'];
        }

        $generalFieldMap = [];

        foreach ($generalFieldMapRaw as $generalFieldMapped) {
            $generalFieldMap[$generalFieldMapped['general_field_identifier']] = $generalFieldMapped['general_field_id'];
        }

        try {
            $form = $this->getExistingForm();

            if ($form) {
                $type = $form->is_template
                    ? CustomForm::__('label.template')
                    : CustomForm::__('label.single');

                $form = $importer->importWithExistingForm(
                    rawForm: $formData,
                    customForm: $form,
                    templateMap: $templateMap,
                    generalFieldMap: $generalFieldMap
                );
            } else {
                $isTemplate = $data['is_template'] ?? false;
                $type = $isTemplate
                    ? CustomForm::__('label.template')
                    : CustomForm::__('label.single');

                $form = $this->importNotExisingForm(
                    $data,
                    $isTemplate,
                    $formData,
                    $importer,
                    $templateMap,
                    $generalFieldMap
                );
            }

            Notification::make()
                ->title($type . '\'' . $form->short_title . '\' wurde erfolgreich importiert')
                ->success()
                ->send();
        } catch (FormImportException $exception) {
            Notification::make()
                ->title($type . ' konnte nicht importiert werden')
                ->danger()
                ->send();

            if (config('app.debug')) {
                throw $exception;
            }
        }

        $file->delete();
    }

    public function hasExistingForm(): bool
    {
        return !is_null($this->getExistingForm());
    }

    public function getExistingForm(): CustomForm|null
    {
        return $this->evaluate($this->existingForm);
    }

    public function getFormSchema(): array
    {
        return [
            CustomFormTypeSelector::make()
                ->afterStateUpdated($this->afterFileUpload(...))
                ->hidden($this->hasExistingForm())
                ->required()
                ->live(),
            FileUpload::make('form_file')
                ->required()
                ->label('Formulardatei')
                ->orientImagesFromExif(false)
                ->storeFiles(false)
                ->deletable(false)
                ->visibility('private')
                ->acceptedFileTypes(['application/json'])
                ->afterStateUpdated($this->afterFileUpload(...))
                ->live(),
            Group::make()
                ->columns()
                ->hidden(fn($get) => empty($get('form_file')) || is_null($this->getDynamicFormConfiguration($get)))
                ->schema([
                    Group::make([
                        TextInput::make('short_title')
                            ->label('Name') //ToDo Translate
                            ->hidden($this->hasExistingForm())
                            ->required()
                            ->live(),
                        TextInput::make('template_identifier')
                            ->label('Template Identifier')//ToDo Translate
                            ->visibleOn('is_template')
                            ->hidden($this->hasExistingForm())
                            ->required(),
                    ]),
                    Group::make([
                        Toggle::make('is_template')
                            ->hidden($this->hasExistingForm())
                            ->disabled(fn($get) => !empty($get('template_map')) || !empty($get('general_field_map')))
                            ->label('ist das Formular ein Template?') //ToDo Translate
                            ->hidden($this->hasExistingForm()),
                        Toggle::make('should_import_rules')
                            ->label('Sollen die Regeln importiert werden?') //ToDo Translate
                            ->default(true)
                    ]),
                    Repeater::make('template_map')
                        ->label('Template Anpassungen') //ToDo Translate
                        ->hiddenOn('is_template')
                        ->reorderable(false)
                        ->deletable(false)
                        ->statePath('template_map')
                        ->addable(false)
                        ->required()
                        ->columns()
                        ->schema([
                            Hidden::make('template_identifier'),
                            TextInput::make('template_identifier_disabled')
                                ->label('Import Identifier') //ToDo Translate
                                ->disabled(),
                            Select::make('template_id')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label('Template') //ToDo Translate
                                ->options($this->getTemplateOptions(...))
                                ->required()
                        ]),
                    Repeater::make('general_field_map')
                        ->label('Generelle Felder Anpassungen') //ToDo Translate
                        ->hiddenOn('is_template')
                        ->reorderable(false)
                        ->deletable(false)
                        ->addable(false)
                        ->required()
                        ->columns()
                        ->schema([
                            Hidden::make('general_field_identifier'),
                            TextInput::make('general_field_identifier_disabled')
                                ->label('Import Identifier') //ToDo Translate
                                ->disabled(),
                            Select::make('general_field_id')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label('Generelles Feld') //ToDo Translate
                                ->options($this->getGeneralFieldOptions(...))
                                ->required()
                        ]),
                ])
        ];
    }

    /**
     * @param $data
     * @param false $isTemplate
     * @param array $formData
     * @param FormSchemaImporter $importer
     * @param array $templateMap
     * @param array $generalFieldMap
     * @return CustomForm
     * @throws FormImportException
     */
    public function importNotExisingForm(
        $data,
        false $isTemplate,
        array $formData,
        FormSchemaImporter $importer,
        array $templateMap,
        array $generalFieldMap
    ): CustomForm {
        $customFormIdentifier = CustomForms::getFormConfiguration($data['custom_form_identifier']);

        $templateIdentifier = $isTemplate ? $data['template_identifier'] : null;
        $shortTitle = $data['short_title'];
        $formInformation = ['short_title' => $shortTitle];

        if ($isTemplate) {
            $formInformation['template_identifier'] = $templateIdentifier;
        } else {
            unset($formData['form']['template_identifier']);
        }

        return $importer->import(
            rawForm: $formData,
            configuration: $customFormIdentifier,
            formInformation: $formInformation,
            templateMap: $templateMap,
            generalFieldMap: $generalFieldMap
        );
    }

    /**
     * @throws JsonException
     */
    protected function getUploadedInfos(?TemporaryUploadedFile $file): array
    {
        if (is_null($file)) {
            return [];
        }

        return json_decode($file->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function getDynamicFormConfiguration(
        Get|null $get = null,
        string $getPath = 'custom_form_identifier'
    ): string|null {
        $existingForm = $this->getExistingForm();

        if (!is_null($existingForm)) {
            return $existingForm->custom_form_identifier;
        }

        if (!is_null($get)) {
            return $get($getPath);
        }

        return null;
    }

    protected function afterFileUpload($get, $set): void
    {
        if (empty($get('form_file')) || is_null($this->getDynamicFormConfiguration($get))) {
            return;
        }

        try {
            $set('should_import_rules', true);

            $importer = FormSchemaImporter::make();

            $file = array_values($get('form_file') ?? [])[0] ?? null;
            $formData = $this->getUploadedInfos($file);

            $rawFields = $formData['fields'] ?? [];
            $fakeForm = new CustomForm();

            $cleanedFields = $importer->importFieldDatas($rawFields, $fakeForm);
            $cleanedFields = collect($cleanedFields);

            //short_title
            $set('short_title', $formData['form']['short_title'] ?? null);

            //is_template / template_identifier
            $templateIdentifier = $formData['form']['template_identifier'] ?? null;
            $set('is_template', !is_null($templateIdentifier));
            $set('template_identifier', $templateIdentifier);

            //template_map
            $usedTemplateIdentifiers = $cleanedFields
                ->whereNotNull('template_id')
                ->pluck('template_id');
            $existingTemplates = CustomForm::query()
                ->whereNotNull('template_identifier')
                ->whereIn('template_identifier', $usedTemplateIdentifiers)
                ->pluck('id', 'template_identifier')
                ->toArray();

            $templateMap = $usedTemplateIdentifiers
                ->map(fn($templateIdentifier) => [
                    'template_identifier_disabled' => $templateIdentifier,
                    'template_identifier' => $templateIdentifier,
                    'template_id' => $existingTemplates[$templateIdentifier] ?? null
                ])
                ->toArray();

            $set('template_map', $templateMap);

            //general_field_map
            $usedGeneralFieldIdentifiers = $cleanedFields
                ->whereNotNull('general_field_id')
                ->pluck('general_field_id');
            $existingGenFields = GeneralField::query()
                ->whereIn('identifier', $usedGeneralFieldIdentifiers)
                ->pluck('id', 'identifier')
                ->toArray();

            $generalFieldMap = $usedGeneralFieldIdentifiers
                ->map(fn($generalFieldIdentifier) => [
                    'general_field_identifier_disabled' => $generalFieldIdentifier,
                    'general_field_identifier' => $generalFieldIdentifier,
                    'general_field_id' => $existingGenFields[$generalFieldIdentifier] ?? null
                ])
                ->toArray();

            $set('general_field_map', $generalFieldMap);
        } catch (Error $exception) {
            Notification::make()
                ->title('Datei ist beschädigt')//ToDo Translate
                ->danger()
                ->send();
            redirect(CustomFormResource::getUrl());
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->action($this->callImportAction(...))
            ->label('Import Formular/Template') //ToDo Translate
            ->form($this->getFormSchema(...))
            ->fillForm(['general_field_map' => [], 'template_map' => []])
            ->modalWidth(MaxWidth::ScreenTwoExtraLarge);
    }

    protected function getTemplateOptions($get): array
    {
        $options = CustomForm::query()
            ->where('custom_form_identifier', $this->getDynamicFormConfiguration($get, '../../custom_form_identifier'))
            ->whereNotNull('template_identifier')
            ->pluck('short_title', 'id')
            ->toArray();

        return array_map(fn($option) => $option ?? '', $options);
    }

    protected function getGeneralFieldOptions($get): array
    {
        $options = GeneralField::query()
            ->whereIn(
                'id',
                GeneralFieldForm::query()
                    ->where(
                        'custom_form_identifier',
                        $this->getDynamicFormConfiguration($get, '../../custom_form_identifier')
                    )
                    ->select('general_field_id')
            )
            ->pluck('name', 'id')
            ->toArray();

        return array_map(fn($option) => $option ?? '', $options);
    }
}
