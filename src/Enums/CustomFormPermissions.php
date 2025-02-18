<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Enums;

enum CustomFormPermissions: string
{
    case FILL_CUSTOM_FORMS = 'custom_forms.fill_custom_forms';
    case MANAGE_CUSTOM_FORMS = 'custom_forms.manage_custom_forms';

    case FILAMENT_LIST_CUSTOM_FORMS = 'custom_forms.filament.list_custom_forms';
    case FILAMENT_EDIT_CUSTOM_FORMS = 'custom_forms.filament.edit_custom_forms';


    case MANAGE_TEMPLATES = 'custom_forms.manage_templates';


}
