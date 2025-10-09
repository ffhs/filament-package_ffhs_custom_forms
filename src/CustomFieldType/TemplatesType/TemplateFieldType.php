<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType;

use Closure;
use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultTemplateDissolveAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

final class TemplateFieldType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;
    use HasStaticMake;

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

    public function getEditorActions(CustomFormConfiguration $formConfiguration, array $state): array
    {
        return array(
            DefaultFieldDeleteAction::make('delete-field')
                ->formConfiguration($formConfiguration),
            DefaultTemplateDissolveAction::make('dissolve-template')
                ->formConfiguration($formConfiguration),

            DefaultCustomActivationAction::make('toggle_active')
                ->visible($this->canBeDeactivate())
                ->formConfiguration($formConfiguration),
        );
    }

    public function getEditorFieldTitle(array $fieldState, CustomFormConfiguration $configuration): string
    {
        return $configuration
            ->getAvailableTemplates()
            ->get($fieldState['template_id'])
            ->short_title ?? '404';
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return false;
    }

    //ToDo check if it works
    public function afterDeleteField(CustomField $field): void
    {
        $templateFields = $field
            ->template
            ->customFields;
        $formFields = $field
            ->customForm
            ->customFields;
        $field
            ->customForm
            ->customFormAnswers
            ->each(fn(CustomFormAnswer $formAnswer) => $formAnswer
                ->customFieldAnswers
                ->whereIn('custom_field_id', $templateFields->pluck('id'))
                ->each($this->getFieldTransferClosure($formFields, $templateFields))
            );
    }

    //ToDo check if it works
    public function afterSaveField(CustomField $field, array $data): void
    {
        $templateFields = $field
            ->template
            ->customFields;
        $formFields = $field
            ->customForm
            ->customFields;

        $field
            ->customForm
            ->customFormAnswers
            ->each(function (CustomFormAnswer $formAnswer) use ($formFields, $templateFields) {
                $templateIdentifiers = $templateFields->pluck('identifier');
                $formFieldIds = $formFields
                    ->whereIn('identifier', $templateIdentifiers)
                    ->pluck('id');
                $formAnswer
                    ->customFieldAnswers
                    ->whereIn('custom_field_id', $formFieldIds)
                    ->each($this->getFieldTransferClosure($templateFields, $formFields));
            });
    }

    protected function getFieldTransferClosure(Collection $newFields, Collection $originalFields): Closure
    {
        return static function (CustomFieldAnswer $fieldAnswer) use ($newFields, $originalFields): void {
            /**@var CustomField $oldField */
            $oldField = $originalFields->firstWhere('id', $fieldAnswer->custom_field_id);

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
