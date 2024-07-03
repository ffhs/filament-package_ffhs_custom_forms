<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRule;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Event\EventType;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\Types\IsType;

abstract class FieldRuleEventType implements EventType
{
     use IsType;


     public static function getConfigTypeList(): string
     {
        return "rules.events";
     }
 }
