<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\NestingObject;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int|null $custom_form_id
 * @property int|null $general_field_id
 * @property int|null $template_id
 * @property string|null $identifier
 * @property string|null $name
 * @property string|null $type
 * @property int|null $form_position
 * @property int|null $layout_end_position
 * @property array<array-key, mixed>|null $options
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CustomFieldAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read CustomForm|null $customForm
 * @property-read Collection<int, CustomOption> $customOptions
 * @property-read int|null $custom_options_count
 * @property-read GeneralField|null $generalField
 * @property-read string $cache_key_for
 * @property-read string $end_container_position
 * @property-read string $position
 * @property-read CustomForm|null $template
 * @property-read mixed $translations
 * @property string[] $overwritten_options
 * @method static Builder<static>|CustomField newModelQuery()
 * @method static Builder<static>|CustomField newQuery()
 * @method static Builder<static>|CustomField query()
 * @method static Builder<static>|CustomField whereCreatedAt($value)
 * @method static Builder<static>|CustomField whereCustomFormId($value)
 * @method static Builder<static>|CustomField whereFormPosition($value)
 * @method static Builder<static>|CustomField whereGeneralFieldId($value)
 * @method static Builder<static>|CustomField whereId($value)
 * @method static Builder<static>|CustomField whereIdentifier($value)
 * @method static Builder<static>|CustomField whereIsActive($value)
 * @method static Builder<static>|CustomField whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomField whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomField whereLayoutEndPosition($value)
 * @method static Builder<static>|CustomField whereLocale(string $column, string $locale)
 * @method static Builder<static>|CustomField whereLocales(string $column, array $locales)
 * @method static Builder<static>|CustomField whereName($value)
 * @method static Builder<static>|CustomField whereOptions($value)
 * @method static Builder<static>|CustomField whereTemplateId($value)
 * @method static Builder<static>|CustomField whereType($value)
 * @method static Builder<static>|CustomField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomField extends ACustomField implements NestingObject, EmbedCustomField
{
    use HasFactory;

    protected $table = 'custom_fields';

    protected $fillable = [
        'name',
        'type',
        'general_field_id',
        'is_active',
        'options',
        'custom_form_id',
        'form_position',
        'layout_end_position',
        'identifier',
        'template_id',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public static function getPositionAttribute(): string
    {
        return 'form_position';
    }

    public static function getEndContainerPositionAttribute(): string
    {
        return 'layout_end_position';
    }

    public static function __(...$args): string
    {
        return CustomForms::__('models.custom_field.' . implode('.', $args));
    }

    //Custom Form
    protected static function booted(): void
    {
        parent::booted();
        self::creating(static function (CustomField $field) {
            //Set identifier key to on other
            if (empty($field->identifier()) && !$field->isGeneralField()) {
                $field->identifier = uniqid('', false);
            }

            return $field;
        });
    }

    public function __get($key)
    {
        if ($key === 'general_field_id') {
            return parent::__get($key);
        }

        if (!$this->isGeneralField()) {
            if ('overwritten_options' === $key) {
                return [];
            }

            return parent::__get($key);
        }

        return match ($key) {
            'name' => $this->generalField->name,
            'type' => $this->generalField->type,
            'options' => $this->getOptionsWithOverwritten(),
            'overwritten_options' => array_keys($this->generalField->overwrite_options ?? []),
            'identifier' => $this->generalField->identifier,
            default => parent::__get($key),
        };
    }

    public function identifier(): string
    {
        return $this->__get('identifier');
    }

    public function isGeneralField(): bool
    {
        return !empty($this->general_field_id);
    }

    public function generalField(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
    }

    public function customForm(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class);
    }

    public function customOptions(): BelongsToMany
    {
        return $this->belongsToMany(CustomOption::class, 'option_custom_field');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }

    public function isTemplate(): bool
    {
        if (!isset($this->template_id)) {
            return false;
        }

        return !empty($this->template_id);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'template_id');
    }

    public function getOptionsWithOverwritten(): array
    {
        $ownOptions = parent::__get('options') ?? [];
        $overwrittenOptions = $this->generalField->overwrite_options ?? [];

        return [
            ... $ownOptions,
            ... $overwrittenOptions,
        ];
    }

    public function getGeneralField(): ?GeneralField
    {
        return $this->generalField;
    }

    public function getTemplate(): ?EmbedCustomForm
    {
        return $this->template;
    }

    public function getCustomOptions(): Collection
    {
        return $this->customOptions;
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        return $this->customForm->getFormConfiguration();
    }
}
