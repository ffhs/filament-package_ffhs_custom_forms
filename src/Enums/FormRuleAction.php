<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Enums;

enum FormRuleAction: string
{
    case LoadData = 'load_data';
    case BeforeRender = 'before_render';
    case AfterRenderForm = 'after_render_form';
    case AfterRenderEntry = 'after_render_entry';
    case OnAnswerLoad = 'on_answer_load';
    case OnAnswerSave = 'on_answer_save';
}
