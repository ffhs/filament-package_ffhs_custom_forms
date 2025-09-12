<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 *
 *
 * @property int $id
 * @property bool $is_template
 * @property string $custom_form_identifier
 * @property string|null $short_title
 * @property string|null $template_identifier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, CustomField> $customFields
 * @property-read int|null $custom_fields_count
 * @property-read Collection<int, CustomFormAnswer> $customFormAnswers
 * @property-read int|null $custom_form_answers_count
 * @property-read Collection<int, FormRule> $formRules
 * @property-read int|null $form_rules_count
 * @property-read Collection<int, GeneralField> $generalFields
 * @property-read int|null $general_fields_count
 * @property-read string $cache_key_for
 * @property-read int|null $owned_fields_count
 * @property-read Collection<int, Rule> $ownedRules
 * @property-read int|null $owned_rules_count
 * @property-read Collection<int, Rule> $rules
 * @property-read int|null $rules_count
 * @property-read mixed $translations
 * @property bool|Collection|mixed $ownedGeneralFields
 * @property bool|\Illuminate\Support\Collection $ownedFields
 * @method static Builder<static>|CustomForm newModelQuery()
 * @method static Builder<static>|CustomForm newQuery()
 * @method static Builder<static>|CustomForm query()
 * @method static Builder<static>|CustomForm whereCreatedAt($value)
 * @method static Builder<static>|CustomForm whereCustomFormIdentifier($value)
 * @method static Builder<static>|CustomForm whereDeletedAt($value)
 * @method static Builder<static>|CustomForm whereId($value)
 * @method static Builder<static>|CustomForm whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomForm whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomForm whereLocale(string $column, string $locale)
 * @method static Builder<static>|CustomForm whereLocales(string $column, array $locales)
 * @method static Builder<static>|CustomForm whereShortTitle($value)
 * @method static Builder<static>|CustomForm whereTemplateIdentifier($value)
 * @method static Builder<static>|CustomForm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomForm extends Model
{
    use HasTranslations;
    use HasFormIdentifier;
    use HasFactory;
    use HasFormRules;
    use HasCustomFields;

    public $translatable = ['not_existing'];

    protected $fillable = [
        'custom_form_identifier',
        'template_identifier',
        'short_title',
    ];

    public static function __(...$args): mixed
    {
        return CustomForms::__('models.custom_form.' . implode('.', $args));
    }

    public function __get($key)
    {
        return match ($key) {
            'is_template' => !is_null($this->template_identifier),
            'ownedRules' => $this->getOwnedRules(),
            'ownedFields' => $this->getOwnedFields(),
            'customFields' => $this->customFields(),
            'rules' => $this->rules(),
            default => parent::__get($key),
        };
    }

    public function isTemplate(): bool
    {
        return !is_null($this->template_identifier);
    }

    public function customFormAnswers(): HasMany
    {
        return $this->hasMany(CustomFormAnswer::class);
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($this->custom_form_identifier);
    }
}
