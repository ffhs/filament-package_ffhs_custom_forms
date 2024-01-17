<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents;

use Illuminate\Support\HtmlString;

class HtmlBadge extends HtmlString
{


    public function __construct($text) {
        $html = '<div style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-1.5 min-w-[theme(spacing.5)] py-0.5 tracking-tight fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 w-max">
                                <span class="grid">
                                    <span class="truncate">
        '.$text.'
                                    </span>
                                </span>

                            </div>';
        parent::__construct($html);
    }

}
