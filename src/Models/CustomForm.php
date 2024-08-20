<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\CachedModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching\HasCacheModel;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rule\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
 * @property Collection customFieldsWithTemplateFields
 * @property Collection rules
 */
class CustomForm extends Model implements CachedModel
{
    use HasCacheModel;
    use HasTranslations;
    use HasFormIdentifier;
    use HasFactory;

    //ToDo cache customFormOptions

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
    ];

    public array $translatable = [
        'does_not_exist' // <= It needs something
    ];

   /* public function __get($key) {
        if($key == 'customFieldsWithTemplateFields') return  $this->cachedFieldsWithTemplates();
        if($key == 'customFields') return  $this->cachedFields();

        return parent::__get($key);
    }*/

   /* public function ownedFields(): HasMany {
        return $this->hasMany(CustomField::class)->orderBy("form_position");
    }*/


    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, (new FormRule())->getTable());
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
    public function cachedGeneralFields(): Collection { // Access with ->generalFields
        $ids = $this->customFields->whereNotNull("general_field_id")->pluck("general_field_id")->toArray();
        return GeneralField::cachedMultiple("id", true, ...$ids);
    }

    public function customFieldInLayout(): HasMany {

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
    }

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
        $cacheKey = $this->getRelationCacheName("fieldsWithTemplates");
        return Cache::remember($cacheKey,
            static::getCacheDuration(),
            function(){
                $customFields = $this->customFields()->get();
                CustomField::addToCachedList($customFields);

                //Cache FieldRules
            /*    $fieldRules = FieldRule::query()->whereIn("custom_field_id", $customFields->pluck("id"))->get(); ToDo new form Rules
                $customFields->each(function(CustomField $customField) use ($fieldRules) {
                    $rules =  $fieldRules->where("custom_field_id", $customField->id);
                    $customField->setValueInManyRelationCache('fieldRules',$rules);
                });*/

                //Cache Templates and Templates Fields
                $templateIds = $customFields->whereNotNull('template_id')->pluck('template_id')->toArray();
                $templates = CustomForm::cachedMultiple("id",true, $templateIds); //To cache the Templates

                $templates->each(function(CustomForm $customForm) use ($customFields) {
                    $fields = $customFields->where("custom_form_id", $customForm->id);
                    $customForm->setValueInManyRelationCache("fieldsWithTemplates", $fields);
                });


                //Cache FieldOptions
                $fieldOptions = CustomOption::query()
                    ->join("option_custom_field", 'custom_option_id', '=', 'custom_options.id')
                    ->whereIn("custom_field_id", $customFields->pluck("id"))
                    ->get();

                $customFields->each(function(CustomField $customField) use ($fieldOptions) {
                    $options =  $fieldOptions->where("custom_field_id", $customField->id);
                    $customField->setValueInManyRelationCache('customOptions',$options);
                });

                return $customFields;
            });
    }


}
