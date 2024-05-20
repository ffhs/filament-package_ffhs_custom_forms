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
            "boolean" => "Yes/No Field",
            "columns_count" => "Number of Columns",
            "column_span" => "Row Span",
            "icon" => "Icon",
            "inline_label" => "Title in Line",
            "inline" => "In One Line",
            "max_length" => "Maximum Length",
            "max_value" => "Maximum Value",
            "min_length" => "Minimum Length",
            "min_value" => "Minimum Value",
            "new_line" => "New Line",
            "show_as_fieldset" => "Show as Fieldset in View",
            "show_in_view" => "Visible in View",
            "show_title" => "Show Title",

            // custom field type
            "color_type" => "Color Format",
            "format" => "Format",
            "only_images" => "Only Images",
            "show_images" => "Show Images",
            "show_images_in_view" => "Show Images in View",
            "downloadable" => "Downloadable",
            "multiple_uploads_allowed" => "Multiple Uploads Allowed",
            "preserve_filenames" => "Preserve Filenames",
            "reorderable" => "Reorderable",
            "editable_keys" => "Editable Keys",
            "editable_values" => "Editable Values",
            "several" => "Several Selectable",
            "min_select" => "Minimum Select",
            "min_select_helper" => "Applies only when (Required)",
            "max_select" => "Maximum Select",
            "max_select_helper" => "'0' means no limit",
            "auto_size" => "Auto Size",
            "suggestions" => "Suggestions",
            "add_suggestion" => "Add Suggestion",
            "columns" => "Columns",
            "toggle_grouped" => "Toggle Grouped",
            "multiple_toggle_selectable" => "Multiple Toggle Selectable",
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
