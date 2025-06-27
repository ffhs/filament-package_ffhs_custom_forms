<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\NoFallBackFormDefinedException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait HasFallbackCustomForm
{
    protected null|Closure|CustomForm $fallbackCustomForm = null;
    protected null|Closure|string $fallbackName = 'new form answer';
    protected bool|Closure $hideOnCreate = true;

    public function isHiddenOnCreate(): bool
    {
        return $this->evaluate($this->hideOnCreate);
    }

    public function hideOnCreate(bool|Closure $hideOnCreate): static
    {
        $this->hideOnCreate = $hideOnCreate;

        return $this;
    }


    public function fallbackCustomForm(null|Closure|CustomForm $fallbackCustomForm): static
    {
        $this->hideOnCreate(false);
        $this->fallbackCustomForm = $fallbackCustomForm;

        return $this;
    }

    public function fallbackCustomName(null|Closure|string $fallbackName): static
    {
        $this->fallbackName = $fallbackName;

        return $this;
    }

    public function getFallbackName(): string
    {
        return $this->evaluate($this->fallbackName);
    }

    public function getFallbackCustomForm(): ?CustomForm
    {
        $fallbackCustomForm = $this->fallbackCustomForm;

        if ($fallbackCustomForm instanceof Closure) {
            $fallbackCustomForm = once(fn() => $this->evaluate($this->fallbackCustomForm));
        }

        if ($fallbackCustomForm === null) {
            if ($this->isHiddenOnCreate()) {
                return null;
            }

            throw new NoFallBackFormDefinedException('No fallback form defined for ' . static::class);
        }

        return $fallbackCustomForm;
    }
}
