<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use League\Uri\UriTemplate\Template;
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
 */
class CustomForm extends Model implements CachedModel
{
    use HasCacheModel;
    use HasTranslations;
    use HasFormIdentifier;
    use HasFactory;

    //ToDo cache customFormOptions
    public static function boot(): void
    {
        parent::boot();
    }


    protected $fillable = [
        'custom_form_identifier',
        'short_title',
        'is_template',
    ];

    protected array $cachedManyRelations = [
        'customFields',
        'ownedFields',
        'generalFields',
        'rules',
        'formRules',
        'rules',
        'ownedRules'
    ];

    public array $translatable = [
        'does_not_exist' // <= It needs something
    ];


    public function ownedRules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, (new FormRule())->getTable());
    }

    public function rules(): BelongsToMany
    {
        $templateIds = CustomField::query()
            ->whereIn("id",  $this->customFields()->select("id"))
            ->whereNotNull("template_id")
            ->select("template_id");

        $ruleIds = FormRule::query()
            ->whereIn("custom_form_id", $templateIds)
            ->orWhere("custom_form_id", $this->id)
            ->select("rule_id");

        return $this->ownedRules()->orWhereIn("rules.id",$ruleIds);
    }

    public function formRules(): HasMany
    {
        return $this->hasMany(FormRule::class);
    }




    public function  getCustomFieldsAsNestedList() : NestedFlattenList{
        return NestedFlattenList::make($this->getOwnedFields(),CustomField::class);
    }


    public function customFields(): Builder {
        $baseQuery = CustomField::query()->where("custom_form_id",$this->id);
        $templateQuery = $baseQuery->clone()->select("template_id")->whereNotNull("template_id");
        return $baseQuery->orWhereIn("custom_form_id",$templateQuery)->orderBy("form_position");
    }



    public function getFormConfiguration():DynamicFormConfiguration{
        return new (DynamicFormConfiguration::getFormConfigurationClass($this->custom_form_identifier))();
    }


    public function customFormAnswers(): HasMany {
        return $this->hasMany(CustomFormAnswer::class);
    }

    public function generalFields(): BelongsToMany {
        return $this->belongsToMany(GeneralField::class, "custom_fields","custom_form_id","general_field_id");
    }

   /* public function customFieldInLayout(): HasMany {

        $subQueryAlLayouts = CustomField::query()
            ->select('form_position','layout_end_position')
            ->where("custom_form_id", $this->id)
            ->where("layout_end_position","!=", null);


        $query = $this->hasMany(CustomField::class)
            ->whereNotIn("id",
                $this->hasMany(CustomField::class)
                    ->select("id")
                    ->rightJoinSub($subQueryAlLayouts, 'sub', function ($join) {
                        $join->on('custom_fields.form_position', '>', 'sub.form_position')
                        ->on('custom_fields.form_position', '<=', 'sub.layout_end_position');
                    })
            );

        return $query;
    }*/

    public static function getTemplateTypesToAdd (string|DynamicFormConfiguration $formType):Collection {
        if($formType instanceof DynamicFormConfiguration)  $formType = $formType::identifier();

        $query=  CustomForm::query()
            ->where("custom_form_identifier", $formType)
            ->where("is_template",true);

        $key ="form_templates_" . $formType;
        $duration = config('ffhs_custom_forms.cache_duration');
        return  Cache::remember($key,$duration, fn() => $query->get());
    }

    public function getOwnedFields(): Collection {
        return $this->cachedCustomFields()->where("custom_form_id", $this->id);
    }

    public function cachedCustomField(int $customFieldId): CustomField|null {
        return $this->cachedCustomFields()->firstWhere("id",$customFieldId);
    }


    public function cachedCustomFields(): Collection {
        $cacheKey = $this->getRelationCacheName("customFields");
        return Cache::remember($cacheKey,
            static::getCacheDuration(),
            function(){
                $customFields = $this->customFields()->get();
                CustomField::addToCachedList($customFields);
                $this->cacheFormRules();

                $this->cacheTemplatesAndTemplatesFields($customFields);
                $this->cacheFieldOptions($customFields);

                Debugbar::info($customFields->count());

                return $customFields;
            });
    }


    protected function cacheFieldOptions(\Illuminate\Database\Eloquent\Collection|array $customFields): void
    {
        //Cache FieldOptions
        $info = DB::table("option_custom_field")
            ->whereIn("custom_field_id", $customFields->pluck("id"))->get();

        if (empty($info)) {
            $fieldOptions = CustomOption::query()
                ->whereIn("id", $info->pluck("custom_option_id"))
                ->get();


            $customFields->each(function (CustomField $customField) use ($info, $fieldOptions) {
                $options = $fieldOptions
                    ->whereIn("id", $info->where("custom_field_id", $customField->id)
                        ->pluck("custom_option_id"));
                $customField->setValueInManyRelationCache('customOptions', $options);
            });
        }
    }


    protected  function cacheTemplatesAndTemplatesFields(\Illuminate\Database\Eloquent\Collection|array $customFields): void
    {
        //Cache Templates and Templates Fields
        $templateIds = $customFields->whereNotNull('template_id')->pluck('template_id')->toArray();

        if(sizeof($templateIds) == 0) return;

        $templates = CustomForm::query()->whereIn("id", $templateIds)->get(); //To cache the Templates
        CustomForm::addToCachedList($templates);

        $templates->each(function (CustomForm $customForm) use ($customFields) {
            $fields = $customFields->where("custom_form_id", $customForm->id);
            $customForm->setValueInManyRelationCache("fieldsWithTemplates", $fields);
        });
    }


    function cacheFormRules(): void
    {
        //Cache FormRules
        $rules = $this->rules;
        if($rules->count() == 0) return;

        Rule::addToCachedList($rules);

        $triggers = RuleTrigger::query()->whereIn("rule_id", $rules->pluck("id"))->get();
        $events = RuleEvent::query()->whereIn("rule_id", $rules->pluck("id"))->get();

        RuleEvent::addToCachedList($events);
        RuleTrigger::addToCachedList($triggers);

        $rules->each(function (Rule $rule) use ($events, $triggers) {
            $ruleEvents = $events->where("rule_id", $rule->id);
            $ruleTriggers = $triggers->where("rule_id", $rule->id);
            $rule->setValueInManyRelationCache('ruleEvents', $ruleEvents);
            $rule->setValueInManyRelationCache('ruleTriggers', $ruleTriggers);
        });
    }


}
