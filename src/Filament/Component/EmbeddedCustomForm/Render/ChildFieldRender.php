<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Illuminate\Support\Collection;

class ChildFieldRender
{
    use CanRenderCustomForm;

    public function __construct(
        protected string $viewMode,
        protected FieldDisplayer $displayer,
        protected CustomForm $customForm,
        protected Collection $customFields,
        protected \Closure $registerRenderedComponents,
        protected int $positionOffset
    ) {
    }

    public static function make(
        string $viewMode,
        FieldDisplayer $displayer,
        CustomForm $customForm,
        Collection $customFields,
        \Closure $registerRenderedComponents,
        int $positionOffset
    ): static {
        return app(static::class, [
            'viewMode' => $viewMode,
            'customForm' => $customForm,
            'customFields' => $customFields,
            'displayer' => $displayer,
            'registerRenderedComponents' => $registerRenderedComponents,
            'positionOffset' => $positionOffset
        ]);
    }

    public function __invoke(): array
    {
        $renderOutput = $this->renderCustomFormRaw(
            $this->viewMode,
            $this->displayer,
            $this->customForm,
            $this->customFields,
            $this->positionOffset
        );
        ($this->registerRenderedComponents)($renderOutput[1]);
        return $renderOutput[0];
    }
}
