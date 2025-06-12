<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestingObject;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers\Identifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm|null $customForm
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption> $customOptions
 * @property-read int|null $custom_options_count
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField|null $generalField
 * @property-read string $cache_key_for
 * @property-read string $end_container_position
 * @property-read string $position
 * @property-read \Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm|null $template
 * @property-read mixed $translations
 * @property string[] $overwritten_options
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereCustomFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereFormPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereGeneralFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereLayoutEndPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomField extends ACustomField implements NestingObject
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


    protected array $cachedRelations = [
        'customOptions',
        'customForm',
        'generalField',
        'template',
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
        self::creating(function (CustomField $field) {#
            //Set identifier key to on other
            if (empty($field->identifier()) && !$field->isGeneralField()) {
                $field->identifier = uniqid();
            }
            return $field;
        });
    }


    //Options

    public function identifier(): string
    {
        return $this->__get('identifier');
    }


    //GeneralField

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


        //PERFORMANCE!!!!
        $genFieldFunction = function (): GeneralField {
            if (!$this->exists) {
                return parent::__get('generalField');
            }
            $genField = GeneralField::getModelCache()?->where('id', $this->general_field_id)->first();
            if (!is_null($genField)) {
                return $genField;
            }

            $generalFieldIds = $this->customForm->customFields->whereNotNull('general_field_id')->pluck(
                'general_field_id'
            );
            $generalFields = GeneralField::query()->whereIn('id', $generalFieldIds)->get();
            GeneralField::addToModelCache($generalFields);
            return GeneralField::cached($this->general_field_id);
        };


        return match ($key) {
            'name' => $genFieldFunction()->name,
            'type' => $genFieldFunction()->type,
            'options' => array_merge(parent::__get($key) ?? [], $genFieldFunction()->overwrite_options ?? []),
            'overwritten_options' => array_keys($genFieldFunction()->overwrite_options ?? []),
            'identifier' => $genFieldFunction()->identifier,
            'generalField' => $genFieldFunction(),
            default => parent::__get($key),
        };
    }

    public function isGeneralField(): bool
    {
        return !empty($this->general_field_id);
    }

    public function customForm(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class);
    }

    public function customOptions(): BelongsToMany
    {
        return $this->belongsToMany(CustomOption::class, 'option_custom_field');
    }


    //Template

    public function isInheritFromGeneralField(): bool
    {
        return !is_null($this->general_field_id);
    }

    public function generalField(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
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
}
