<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Support\Collection;

/**
 * @property bool $is_active
 * @property mixed|null $layout_end_position
 * @property mixed|null $form_position
 * @property mixed|null $identifier
 */
interface EmbedCustomField
{
    public function getType(): ?CustomFieldType;

    public function isGeneralField(): bool;

    public function getGeneralField(): ?GeneralField;

    public function getTemplate(): ?EmbedCustomForm;

    public function getCustomOptions(): Collection;

    public function getFormConfiguration(): CustomFormConfiguration;

    public function isTemplate(): bool;

    public function identifier(): string;

    public function getFormPosition(): int;

    public function getLayoutEndPosition(): ?int;

    public function isActive(): bool;

    public function getOptions(): array;

    public function setOptions(array $options): void;

    public function getName(): ?string;
}
