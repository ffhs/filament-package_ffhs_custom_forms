<?php
//EN

return [

    'functions'=>[
        'connect'=>'Connect',
    ],

    "navigation"=>[
        'general_fields'=> "General Fields",
        'forms' => 'Forms',
        'group' => [
            'forms'=> 'Forms'
        ]
    ],

    "fields" =>[
        'type' => 'Field Type',
        'name' => 'Name',
        'tool_tip'=> 'Short Description',
        'identify_key'=> 'Identification Key',
        'is_general_field_active' => 'Active',
        'label'=> "Name",
        'form_connections'=> 'Linked Forms',
        'general_field' => 'General Field',

        'helper_text' => [
            'type'=> 'The field type of the field. WARNING: This cannot be changed after creation.',
            'identify_key'=> 'This key is required for exporting the data.',
            'is_general_field_active'=> 'If this is deactivated, all general fields based on this field will be deactivated.',
        ],

        'types'=>[
            "text" => "Text",
            "email" => "Email",
            "number" => "Number",
            "select" => "Selection",
            "checkbox" => "Checkbox",
            "radio" => "Radio",
            "date" => "Date",
            "date-time" => "Date and Time",
            "textarea" => "Textarea",
            "module_select" => "Module Selector",
        ],
    ],

    'form'=>[
        'custom_form_identifier'=> [
            'display_name' => "Form Type Name",
            'raw_name' => "Form Type Identifier"
        ]
    ]

];
