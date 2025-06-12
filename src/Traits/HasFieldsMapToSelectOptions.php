<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Support\Collection;

trait HasFieldsMapToSelectOptions
{



    protected function getSelectOptionsFromFields(Collection $customFields, string $formName = ''): array
   {
       $options = [];
       foreach ($customFields as $field){
           /**@var CustomField $field*/
           $title = '';
           if(is_null($field?->customForm)) $title = $formName;
           if(empty($title)) $title = $field?->customForm?->short_title;
           if(empty($title)) $title = '?';

           if($field->template_id == null) $options[$title][$field->identifier] = $field->name ?? '?';
           else $options[$title][$field->identifier] = $field->template->short_title ?? '?';
       }



       return $options;
   }

}
