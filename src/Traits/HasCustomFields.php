<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCustomFields
{
    public function ownedFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function customFields(array $with = []): Collection
    {
        if ($this->relationLoaded('customFields')) {
            return $this->getRelation('customFields');
        }

        $templateIdQueries = CustomField::query()
            ->select('template_id')
            ->where('custom_form_id', $this->id);

        $customFieldQuery = CustomField::query()
            ->where(function (Builder $query) use ($templateIdQueries) {
                $query
                    ->where('custom_form_id', $this->id)
                    ->orWhereIn('custom_form_id', $templateIdQueries);
            });

        $templates = CustomForm::query()
            ->whereIn('id', $templateIdQueries)
            ->get()
            ->keyBy('id');

        $customFields = $customFieldQuery
            ->with($with)
            ->get();


        $groupedCustomFields = $customFields->groupBy('custom_form_id');
        $this->setRelation('ownedFields', $groupedCustomFields->get($this->id));
        $templates->each(function (CustomForm $template) use ($groupedCustomFields) {
            $fields = $groupedCustomFields->get($template->id);
            $template->setRelation('ownedFields', $fields);
            $template->setRelation('customFields', $fields);
        });

        $this->setRelation('customFields', $customFields);

        $customFields->each(function (CustomField $customField) use ($templates) {
            if ($customField->custom_form_id === $this->id) {
                $customField->setRelation('customForm', $this);
            } else {
                $customField->setRelation('customForm', $templates->get($customField->custom_form_id));
            }
            if ($customField->template_id) {
                $customField->setRelation('template', $templates->get($customField->template_id));
            }
        });

        return $customFields;
    }

    public function customFieldsQuery(): Collection
    {
        $templateIdQueries = CustomField::query()
            ->select('template_id')
            ->where('custom_form_id', $this->id);

        return CustomField::query()
            ->where(function (Builder $query) use ($templateIdQueries) {
                $query
                    ->where('custom_form_id', $this->id)
                    ->orWhereIn('custom_form_id', $templateIdQueries);
            });
    }

    public function getOwnedFields(): Collection
    {
        if ($this->relationLoaded('ownedFields')) {
            return parent::__get('ownedFields');
        }

        if ($this->relationLoaded('customFields')) {
            $ownedFields = parent::__get('customFields')->where('custom_form_id', $this->id);
            $this->setRelation('customFields', $ownedFields);
            return $ownedFields;
        }

        return parent::__get('customFields');
    }

    public function ownedGeneralFields(): BelongsToMany
    {
        return $this->belongsToMany(GeneralField::class, 'custom_fields', 'custom_form_id', 'general_field_id');
    }

    public function generalFields(): BelongsToMany
    {
        $generalFields = $this->customFields()
            ->select('general_field_id')
            ->whereNotNull('general_field_id');

        return $this->belongsToMany(GeneralField::class, 'custom_fields', 'custom_form_id', 'general_field_id')
            ->orWhereIn('id', $generalFields);
    }
}
