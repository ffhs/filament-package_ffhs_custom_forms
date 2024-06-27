<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

trait UseComponentInjection
{
    protected mixed $injection;

    protected static function injectIt(mixed $injection, array $data):static {
        $static = app(static::class, $data);
        /**@var static $static*/
        $static->setInjection($injection);
        $static->configure();
        return $static;
    }

    private function setInjection($form):void {
        $this->injection = $form;
    }

}
