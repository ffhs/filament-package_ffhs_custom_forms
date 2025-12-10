<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;

trait UseAutosaveCustomForm
{
    protected bool|Closure $isAutoSaving = false;

    public function autoSave(bool|Closure $isAutoSaving = true): static
    {
        $this->isAutoSaving = $isAutoSaving;

        return $this;
    }

    public function isAutoSaving(): bool
    {
        return $this->evaluate($this->isAutoSaving) && $this->relationship;
    }

    /**
     * @param array<string, mixed> $state
     * @return void
     */
    protected function runAutoSave(array $state): void
    {
        if (!$this->isAutoSaving()) {
            return;
        }
        if ($this->getRelationshipName()) {
            $this->saveCustomFormAnswerRelation($state);
        }
    }
}
