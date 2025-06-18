<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultTemplateDissolveAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

final class TemplateFieldType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'template';
    }

    public function viewModes(): array
    {
        return [
            'default' => TemplateTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'carbon-copy-file';
    }

    public function isFullSizeField(): bool
    {
        return true;
    }

    public function mutateCustomFieldDataOnSave(CustomField $field, array $data): array
    {
        unset($data['options']);
        return $data;
    }

    public function getEditorActions(string $key, array $fieldState): array
    {
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            DefaultTemplateDissolveAction::make('dissolve-template-' . $key),
            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }

    public function getEditorFieldTitle(array $fieldData, CustomForm $form): string
    {
        return $form
            ->getFormConfiguration()
            ->getAvailableTemplates()
            ->get($fieldData['template_id'])
            ->short_title;
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return false;
    }

    //ToDo check if it works
    public function afterDeleteField(CustomField $field): void
    {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;
        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use (
            $formFields,
            $field,
            $templateFields
        ) {
            $formAnswer->customFieldAnswers
                ->whereIn('custom_field_id', $templateFields->pluck('id'))
                ->each($this->getFieldTransferClosure($formFields, $templateFields));
        });
    }

    //ToDo check if it works
    public function afterSaveField(CustomField $field, array $data): void
    {
        $templateFields = $field->template->customFields;
        $formFields = $field->customForm->customFields;

        $field->customForm->customFormAnswers->each(function (CustomFormAnswer $formAnswer) use (
            $formFields,
            $field,
            $templateFields
        ) {
            $templateIdentifiers = $templateFields->pluck('identifier');
            $formFieldIds = $formFields->whereIn('identifier', $templateIdentifiers)->pluck('id');
            $formAnswer->customFieldAnswers
                ->whereIn('custom_field_id', $formFieldIds)
                ->each($this->getFieldTransferClosure($templateFields, $formFields));
        });
    }

    protected function getFieldTransferClosure(Collection $newFields, Collection $originalFields): Closure
    {
        return static function (CustomFieldAnswer $fieldAnswer) use ($newFields, $originalFields): void {
            /**@var CustomField $oldField */
            $oldField = $originalFields->where('id', $fieldAnswer->custom_field_id)->first();
            if (is_null($oldField)) {
                return;
            }


            $identifier = $oldField->identifier;
            $newField = $newFields
                ->filter(fn(CustomField $customField) => $customField->identifier === $identifier)
                ->first();

            if (is_null($newField)) {
                return;
            }
            $fieldAnswer->custom_field_id = $newField->id;
            $fieldAnswer->save();
        };
    }

    protected function getEditorFieldBadgeColor(array $rawData): ?array
    {
        return Color::rgb('rgb(34, 135, 0)');
    }

    protected function getEditorFieldBadgeText(array $rawData): ?string
    {
        return 'Template';
    }
}
