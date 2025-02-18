<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Enums;

enum CustomFormPermissionName: string
{
    case FILL_CUSTOM_FORMS = 'custom_forms.fill_custom_forms';
    case MANAGE_CUSTOM_FORMS = 'custom_forms.manage_custom_forms';
    case MANAGE_TEMPLATES = 'custom_forms.manage_templates';

    case FILAMENT_RESOURCE_CUSTOM_FORMS = 'custom_forms.filament.custom_forms';
    case FILAMENT_RESOURCE_TEMPLATES = 'custom_forms.filament.templates';


}
