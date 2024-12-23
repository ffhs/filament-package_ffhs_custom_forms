<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\RelationCachedInformations;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $custom_form_identifier
 * @property string|null $short_title
 * @property Collection $customFormAnswers
 * @property Collection $customFields
 * @property Collection $generalFields
 * @property bool $is_template
 * @property Collection $customFieldInLayout
 * @property Collection rules
 * @property Collection ownedRules
 * @property Collection $ownedFields
 * @property string|null $template_identifier
 */
class CustomForm extends Model implements CachedModel
{
    use HasCacheModel;
    use HasTranslations;
    use HasFormIdentifier;
    use HasFactory;


    public array $translatable = [
        'does_not_exist' // <= It needs something     ToDo Realy?
    ];

    protected $fillable = [
        'custom_form_identifier',
        'template_identifier',
        'short_title',
    ];

    //ToDo cache customFormOptions
    protected array $cachedRelations = [
        'customFields',
        'generalFields',
        'rules',
        'formRules',
        'ownedRules',
    ];

    public static function getTemplateTypesToAdd (string|DynamicFormConfiguration $formType):Collection {
        if($formType instanceof DynamicFormConfiguration)  $formType = $formType::identifier();

        $query=  CustomForm::query()
            ->where('custom_form_identifier', $formType)
            ->whereNotNull('template_identifier');

        $key ='form_templates_' . $formType;
        $duration = config('ffhs_custom_forms.cache_duration');
        return  Cache::remember($key,$duration, fn() => $query->get());
    }

    public function __get($key)
    {
        if($key === 'is_template') return !is_null($this->template_identifier);
        else return parent::__get($key);
    }

    public function ownedFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function rules(): BelongsToMany
    {
        $templateIds = CustomField::query()
            ->whereIn('id',  $this->customFields()->select('id'))
            ->whereNotNull('template_id')
            ->select('template_id');

        $ruleIds = FormRule::query()
            ->whereIn('custom_form_id', $templateIds)
            ->orWhere('custom_form_id', $this->id)
            ->select('rule_id');

        return $this->ownedRules()->orWhereIn('rules.id',$ruleIds);
    }

    public function customFields(): HasMany
    {
        $baseQuery = CustomField::query()->where('custom_form_id',$this->id);
        $templateQuery = $baseQuery->clone()->select('template_id')->whereNotNull('template_id');
        return $this->hasMany(CustomField::class)
            ->orWhereIn('custom_form_id',$templateQuery)
            ->orderBy('form_position');
    }

    public function ownedRules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, (new FormRule())->getTable());
    }

    public function formRules(): HasMany
    {
        return $this->hasMany(FormRule::class);
    }

    public function  getCustomFieldsAsNestedList() : NestedFlattenList{
        return NestedFlattenList::make($this->getOwnedFields(),CustomField::class);
    }

    public function getOwnedFields(): Collection {
        return $this->customFields->where('custom_form_id', $this->id);
    }

    public function getFormConfiguration():DynamicFormConfiguration{
        return new (DynamicFormConfiguration::getFormConfigurationClass($this->custom_form_identifier))();
    }

    public function customFormAnswers(): HasMany {
        return $this->hasMany(CustomFormAnswer::class);
    }

    public function generalFields(): BelongsToMany {
        return $this->belongsToMany(GeneralField::class, 'custom_fields','custom_form_id','general_field_id');
    }

    public function cachedCustomFields(): RelationCachedInformations|Collection {

        $cacheKey = $this->getCacheKeyForAttribute('customFields');
        return Cache::remember($cacheKey,
            static::getCacheDuration(),
            function(){

                $customFields = $this->customFields()->get();

                CustomField::addToModelCache($customFields);

                $this->cacheTemplatesAndTemplatesFields($customFields);

                $this->cacheGeneralFields($customFields);

                $this->cacheFieldOptions($customFields);

                $this->cacheFormRules($customFields);

                return new RelationCachedInformations(CustomField::class, $customFields->pluck('id')->toArray());
            });
    }

    protected  function cacheTemplatesAndTemplatesFields(Collection $customFields): void
    {
        //Cache Templates and Templates Fields
        $templateIds = $customFields->whereNotNull('template_id')->pluck('template_id')->toArray();

        if(sizeof($templateIds) == 0) return;

        $templates = CustomForm::query()->whereIn('id', $templateIds)->get()->add($this); //To cache the Templates

        $templates = $templates->keyBy('id');

        $fieldGroupByForm = $customFields->groupBy('custom_form_id');
        foreach ($fieldGroupByForm as $formId => $fields){
            $customForm = $templates[$formId];

            $fieldIds = $fields->pluck('id')->toArray();

            $fieldRelations = new RelationCachedInformations(CustomField::class, $fieldIds);
            $customForm->setCacheValue('ownedFields', $fieldRelations);
            if($formId != $this->id) $customForm->setCacheValue('customFields', $fieldRelations);
        }

        CustomForm::addToModelCache($templates);
    }

    protected function cacheGeneralFields(Collection &$customFields): void
    {
        $generalFieldForms = GeneralFieldForm::query()
            ->where('custom_form_identifier', $this->custom_form_identifier)
            ->get();

        if(empty($generalFieldForms)) $generalFields = collect();
        else $generalFields = GeneralField::query()
                ->whereIn('id', $generalFieldForms->pluck('general_field_id'))
                ->get();


        GeneralField::addToModelCache($generalFields);

        $formIds = $customFields->whereNotNull('template_id')->pluck('template_id')->add($this->id);
        $forms = CustomForm::getModelCache()->whereIn('id', $formIds);


        $customFieldsWithGenField = $customFields->whereNotNull('general_field_id');

        $forms ->each(function (CustomForm $form) use ($customFieldsWithGenField): void {
                $genIds = $customFieldsWithGenField
                    ->where('custom_form_id', $form->id)
                    ->pluck('general_field_id')
                    ->toArray();

                $genFields = new RelationCachedInformations(GeneralField::class, $genIds);
                $form->setCacheValue('generalFields', $genFields);
            });

    }

    protected function cacheFieldOptions(\Illuminate\Database\Eloquent\Collection|array $customFields): void
    {
        //Cache FieldOptions
        $info = DB::table('option_custom_field')
            ->whereIn('custom_field_id', $customFields->pluck('id'))->get();

        //Cache Options Relations
        if (empty($info)){
            foreach ($customFields as $customField) {
                if(!($customField->getType() instanceof CustomOptionType)) continue;
                $options = new RelationCachedInformations(CustomOption::class, []);
                $customField->setCacheValue('customOptions', $options);
            }
            return;
        }

        //Cache Options
        $fieldOptions = CustomOption::query()
                ->whereIn('id', $info->pluck('custom_option_id'))
                ->get();
        CustomOption::addToModelCache($fieldOptions);

        $fieldGrouped = $info->groupBy('custom_field_id');

        //Cache Options Relations
        foreach ($customFields as $customField) {
            if(!is_subclass_of($customField->getTypeClass() , CustomOptionType::class)) continue;

            if($fieldGrouped->has($customField->id))
                $optionIds = $fieldGrouped[$customField->id]->pluck('custom_option_id')->toArray();
            else $optionIds =  [];

            //$optionIds = $fieldGrouped[$customField->id]->pluck('custom_option_id')->toArray();
            $options = new RelationCachedInformations(CustomOption::class, $optionIds);
            $customField->setCacheValue('customOptions', $options);
        }
    }

    protected function cacheFormRules(Collection $customFields): void
    {
        $formIds = $customFields->whereNotNull('template_id')->pluck('template_id')->add($this->id);
        $formRules = FormRule::query()->whereIn('custom_form_id', $formIds);
        $rules = Rule::query()->whereIn('id', $formRules->select('rule_id'))->get();

        //Cache FormRules
        $this->setCacheValue('rules', $rules);
        $this->relations['rules'] = $rules;

        if($rules->count() == 0) return;

        Rule::addToModelCache($rules);

        $triggers = RuleTrigger::query()->whereIn('rule_id', $rules->pluck('id'))->get();
        $events = RuleEvent::query()->whereIn('rule_id', $rules->pluck('id'))->get();

        RuleEvent::addToModelCache($events);
        RuleTrigger::addToModelCache($triggers);


        $groupedEvents =  $events->groupBy('rule_id');
        $groupedTriggers =  $triggers->groupBy('rule_id');
        foreach ($groupedEvents as $ruleId => $ruleEvents){
            $rule = $rules->firstWhere('id', $ruleId);
            $ruleEvents = new RelationCachedInformations(RuleEvent::class, $ruleEvents->pluck('id')->toArray());
            $rule->setCacheValue('ruleEvents', $ruleEvents);
        }

        foreach ($groupedTriggers as $ruleId => $ruleTriggers){
            $rule = $rules->firstWhere('id', $ruleId);
            $ruleTriggers = new RelationCachedInformations(RuleTrigger::class, $ruleTriggers->pluck('id')->toArray());
            $rule->setCacheValue('ruleTriggers', $ruleTriggers);
        }
    }




}
