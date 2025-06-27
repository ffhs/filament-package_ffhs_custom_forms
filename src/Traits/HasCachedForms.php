<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Collection;

trait HasCachedForms
{
    protected Collection $customForms;

    public function getCustomFormFromId(int $id): ?CustomForm
    {
        if (!isset($this->customForms)) {
            $this->customForms = collect();
        }

        if ($form = $this->customForms->get($id)) {
            return $form;
        }

        $form = CustomForm::firstWhere('id', $id);
        $this
            ->customForms
            ->put($id, $form);

        return $form;
    }

    public function cacheForm(CustomForm|Collection $customForm): void
    {
        if (!isset($this->customForms)) {
            $this->customForms = collect();
        }

        if ($customForm instanceof CustomForm) {
            $this
                ->customForms
                ->put($customForm->id, $customForm);

            return;
        }

        $customForms = $customForm->keyBy('id');
        $this->customForms = $this
            ->customForms
            ->merge($customForms);
    }
}
