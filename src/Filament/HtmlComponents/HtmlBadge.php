<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents;

use Illuminate\Support\HtmlString;

class HtmlBadge extends HtmlString
{

    public function __construct(string $text, ?array $color=null) {
       if(is_null($color))$style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);";
       else $style = '--c-50:'.$color[50].';--c-400:'.$color[400].';--c-600:'.$color[600].';';
        $html ='
            <div style="'.$style.' margin-right: 8px;" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs
            font-medium ring-1 ring-inset px-1.5 min-w-[theme(spacing.5)]  tracking-tight fi-color-custom bg-custom-50
            text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 w-max ">
                <span class="grid">
                    <span class="truncate">
                         '.$text.'
                    </span>
                </span>
            </div>
            ';
        parent::__construct($html);
    }


}
