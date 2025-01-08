<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CustomFormImportAction extends Action
{

    public function callExportAction($data): void
    {

        $customFormIdentifier = new (DynamicFormConfiguration::getFormConfigurationClass($data['custom_form_identifier']))();
        $formData = $this->getUploadedInfos($data['form_file']);
        $importer = FormSchemaImporter::make();
        $formInformation = ['short_title' => $data['short_title']];
        $generalFieldMap = [];
        $templateMap = [];

        if($data['is_template']){
              $formInformation['template_identifier'] = $data['template_identifier'];
        }else{
              unset($formData['template_identifier']);
        }

        dd($data);

        $form = $importer->import(
            rawForm: $formData,
            configuration: $customFormIdentifier,
            formInformation: $formInformation,
            generalFieldMap: $generalFieldMap,
            templateMap: $templateMap
        );

//        $type = $record->is_template ? 'Template' : 'Formular';
//        $fileName =   $record->short_title . ' - ' .$type .' '. date('Y-m-d H:i') .'.json';


        Notification::make()
//            ->title($type .' wurde erfolgreich exportiert')//ToDo Translate
            ->success()
            ->send();
    }

    protected function getUploadedInfos(?TemporaryUploadedFile $file): array
    {
        if(is_null($file)) return [];
        /** @var TemporaryUploadedFile $file */
        return json_decode(json: $file->getContent(), associative: true);
    }

    public static function make(?string $name = 'import_custom_form'): static
    {
        return parent::make($name);
    }

    public function getFormSchema(): array
    {
        return [
            CustomFormTypeSelector::make()
                ->afterStateUpdated($this->afterFileUpload(...))
                ->required()
                ->live(),

            FileUpload::make('form_file')
                ->required()
                ->label('Formular Datei')
                ->orientImagesFromExif(false)
                ->storeFiles(false)
                ->deletable(false)
                ->visibility('private')
                ->acceptedFileTypes(['application/json'])
                ->afterStateUpdated($this->afterFileUpload(...))
                ->live(),

            Group::make()
                ->columns()
                ->hidden(fn($get) => empty($get('form_file')) || is_null($get('custom_form_identifier')))
                ->schema([

                    Group::make([
                        TextInput::make('short_title')
                            ->label('Name') //ToDo Translate
                            ->required()
                            ->live(),


                        TextInput::make('template_identifier')
                            ->label('Template Identifier')//ToDo Translate
                            ->visibleOn('is_template')
                            ->required(),
                    ]),

                    Group::make([
                        Toggle::make('is_template')
                            ->disabled(fn($get) => !$get('template_map')?->isEmpty() || !$get('general_field_map')?->isEmpty())
                            ->label('ist das Fomular ein Template?') , //ToDo Translate

                        Toggle::make('import_rules')
                            ->label('Sollen die Regeln importiert werden?'), //ToDo Translate
                    ]),



                    Repeater::make('template_map')
                        ->label('Template Anpassungen') //ToDo Translate
                        ->hiddenOn('is_template')
                        ->reorderable(false)
                        ->deletable(false)
                        ->addable(false)
                        ->columns()
                        ->schema([
                            TextInput::make('template_identifier')
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
                        ->columns()
                        ->schema([
                            TextInput::make('general_field_identifier')
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

    protected function afterFileUpload($get, $set): void
    {
        try {
            $importer = FormSchemaImporter::make();

            $file = array_values($get('form_file')?? [])[0] ?? null;
            $formData = $this->getUploadedInfos($file);

            $rawFields = $formData['fields']?? [];
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
                ->pluck('id', 'template_identifier')->toArray();

            $templateMap = $usedTemplateIdentifiers->map(function ($templateIdentifier) use ($existingTemplates) {
                return [
                    'template_identifier' => $templateIdentifier,
                    'template_id' => $existingTemplates[$templateIdentifier] ?? null
                ];
            });

            $set('template_map', $templateMap);


            //general_field_map
            $usedGeneralFieldIdentifiers = $cleanedFields
                ->whereNotNull('general_field_id')
                ->pluck('general_field_id');
            $existingGenFields = GeneralField::query()
                ->whereIn('identifier', $usedGeneralFieldIdentifiers)
                ->pluck('id', 'identifier')->toArray();

            $generalFieldMap = $usedGeneralFieldIdentifiers->map(function ($generalFieldIdentifier) use ($existingGenFields) {
                return [
                    'general_field_identifier' => $generalFieldIdentifier,
                    'general_field_id' => $existingGenFields[$generalFieldIdentifier] ?? null
                ];
            });

            $set('general_field_map', $generalFieldMap);



        }catch (\Error $exception){
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
        $this->action($this->callExportAction(...));
        $this->label('Import Formular/Template'); //ToDo Translate

        $this->form($this->getFormSchema(...));
        $this->modalWidth(MaxWidth::ScreenTwoExtraLarge);
    }


    protected function getTemplateOptions($get): array
    {
        $options = CustomForm::allCached()
            ->where('custom_form_identifier',$get('../../custom_form_identifier'))
            ->whereNotNull('template_identifier')
            ->pluck('short_title', 'id')
            ->toArray();

         return array_map(fn($option) => $option ?? '',$options);
    }

    protected function getGeneralFieldOptions($get): array
    {
        $options =  GeneralField::allCached()
            ->whereIn(
                'id',
                GeneralFieldForm::allCached()
                    ->where('custom_form_identifier', $get('../../custom_form_identifier'))
                    ->pluck('general_field_id')
            )
            ->pluck('name', 'id')->toArray();
        return array_map(fn($option) => $option ?? '',$options);
    }
}
