<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasFormRules
{
    public function formRules(): HasMany
    {
        return $this->hasMany(FormRule::class);
    }

    public function ownedRules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, app(FormRule::class)->getTable())
            ->with(['ruleTriggers', 'ruleEvents']);
    }

    public function getOwnedRules(): Collection
    {
        if ($this->relationLoaded('ownedRules')) {
            return parent::__get('ownedRules');
        }

        if ($this->relationLoaded('rules')) {
            $ownedRules = $this
                ->getRules()
                ->whereIn('id', $this->formRules->pluck('rule_id'));

            $this->setRelation('ownedRules', $ownedRules);

            return $ownedRules;
        }

        return parent::__get('ownedRules');
    }

    public function rules(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('rules')) {
            return $this->getRelation('rules');
        }

        $rules = $this
            ->getRulesQuery()
            ->get();
        $this->setRelation('rules', $rules);

        return $rules;
    }

    public function getRulesQuery(): Builder
    {
        $templateIdQueries = CustomField::query()
            ->select('template_id')
            ->where('custom_form_id', $this->id);
        $formRulesQuery = FormRule::query()
            ->where('custom_form_id', $this->id)
            ->orWhereIn('custom_form_id', $templateIdQueries)
            ->select('rule_id');

        return Rule::query()
            ->whereIn('id', $formRulesQuery)
            ->with('ruleEvents')
            ->with('ruleTriggers');
    }
}
