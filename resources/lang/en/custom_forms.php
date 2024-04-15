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
        ],
        "templates"=> "Templates"
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
        'is_required'=> 'Required',

        'helper_text' => [
            'type'=> 'The field type of the field. ATTENTION: This cannot be changed after creation',
            'identify_key'=> 'This key is required to export the data',
            'is_general_field_active'=> 'If this is deactivated, all general fields based on this field will be deactivated.',
        ],

        'types'=>[
            "text" => "Text",
            "email" => "Email",
            "number" => "Number",
            "select" => "Select",
            "checkbox" => "Checkbox",
            "section"=>"Section",
            "radio" => "Radio",
            "date" => "Date",
            "date-time" => "Date and Time",
            "textarea" => "Textarea",
            "icon-select" => "Icon Selector",
            "checkbox_list" => "Checkbox List",
            "toggle_buttons"=> "Toggle Buttons",
            'tags_input'=> 'Tags',
            'color_input'=>'Color picker',
            'key_value'=> 'Key value',
            'title' => 'Title',
            'layout_text'=>'Layout text',
            'fieldset' => 'Fieldset',
            'group' => 'Group',
        ],

        'rules'=>[
            "is_disabled_rule" => "Disable field",
            "is_hidden_rule" => "Hide field",
            "is_required_rule" => "Require field",
            'change_options_rule' => 'Change field options'
        ],

        'anchors'=>[
            'value_equals_anchor' => 'equal value anchor'
        ]
    ],

    'form'=>[
        'custom_form' =>'Form',
        'short_title'=> 'Title',
        'custom_fields_amount'=> 'Number of Added Fields',
        'custom_form_identifier'=> [
            'display_name' => "Form Type Name",
            'raw_name' => "Form Type Identifier"
        ],
        'template'=> "Template"
    ]

];
