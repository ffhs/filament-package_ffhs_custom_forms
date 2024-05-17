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
            "date-time" => "Date and time",
            "textarea" => "Textarea",
            "icon-select" => "Icon selector",
            "checkbox_list" => "Checkbox list",
            "toggle_buttons"=> "Toggle buttons",
            'tags_input'=> 'Tags',
            'color_input'=>'Color picker',
            'key_value'=> 'Key value',
            'title' => 'Title',
            'layout_text'=>'Layout text',
            'fieldset' => 'Fieldset',
            'group' => 'Group',
            'tabs' => 'Tabs',
            'tab' => 'Tab',
            'wizard' => 'Step for step',
            'wizard_step' => 'Step',
            'download_file' => 'Download field',
            'image_layout' => 'Image layout',
            'space' => 'Space',
            'file_upload' => 'File upload',
        ],

        'rules'=>[
            "is_disabled_rule" => "Disable field",
            "is_hidden_rule" => "Hide field",
            "is_required_rule" => "Require field",
            'change_options_rule' => 'Change field options'
        ],

        'anchors'=>[
            'value_equals_anchor' => 'equal value anchor'
        ],


        'type_options' => [
            "boolean" => "Ja/Nein Feld",
            "columns" => "Anzahl Spalten",
            "column_span" => "Zeilenweite",
            "icon" => "Icon",
            "inline_label" => "Title in der Zeile",
            "inline" => "In einer Zeile",
            "max_length" => "Maximale Länge",
            "max_value" => "Maximaler Wert",
            "min_length" => "Minimale Länge",
            "min_value" => "Minimaler Wert",
            "new_line" => "Neue Zeile",
            "show_as_fieldset" => "Als Fieldset beim Betrachten anzeigen",
            "show_in_view" => "Sichtbar beim Betrachten",
            "show_title" => "Titel Anzeigen",
        ],
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
