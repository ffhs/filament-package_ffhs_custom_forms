<?php
//EN

return [

    'functions'=>[
        'connect'=>'Connect',
        'add' => 'Add',
    ],

    'navigation'=>[
        'general_fields'=> 'General Fields',
        'forms' => 'Forms',
        'group' => [
            'forms'=> 'Forms'
        ],
        'templates'=> 'Templates'
    ],

    'fields' =>[
        'type' => 'Field Type',
        'name' => 'Name',
        'tool_tip'=> 'Short Description',
        'identifier'=> 'Identification Key',
        'is_general_field_active' => 'Active',
        'label'=> 'Name',
        'form_connections'=> 'Linked Forms',
        'general_field' => 'General Field',
        'is_required'=> 'Required',

        'helper_text' => [
            'type'=> 'The field type of the field. ATTENTION: This cannot be changed after creation',
            'identifier'=> 'This key is required to export the data',
            'is_general_field_active'=> 'If this is deactivated, all general fields based on this field will be deactivated.',
        ],

        'type_view' =>[
            'select' => [
                'select' => 'Select'
            ]
        ],

        'types'=>[
            'text' => 'Text',
            'email' => 'Email',
            'number' => 'Number',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'section'=>'Section',
            'radio' => 'Radio',
            'date' => 'Date',
            'date-time' => 'Date and time',
            'textarea' => 'Textarea',
            'icon-select' => 'Icon selector',
            'checkbox_list' => 'Checkbox list',
            'toggle_buttons'=> 'Toggle buttons',
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
            'is_disabled_rule' => 'Disable field',
            'is_hidden_rule' => 'Hide field',
            'is_required_rule' => 'Require field',
            'change_options_rule' => 'Change field options',
            'always' => 'Always active',
        ],

        'anchors'=>[
            'value_equals_anchor' => 'equal value anchor',
            'infolist_view' => 'if is infolist'
        ],

        'type_options' => [
            'boolean' => 'Yes/No Field',
            'columns_count' => 'Number of Columns',
            'column_span' => 'Row Span',
            'icon' => 'Icon',
            'inline_label' => 'Title in Line',
            'inline' => 'In One Line',
            'max_length' => 'Maximum Length',
            'max_value' => 'Maximum Value',
            'min_length' => 'Minimum Length',
            'min_value' => 'Minimum Value',
            'new_line' => 'New Line',
            'show_as_fieldset' => 'Show as Fieldset in View',
            'show_in_view' => 'Visible in View',
            'show_title' => 'Show Title',

            // custom field type
            'color_type' => 'Color Format',
            'format' => 'Format',
            'only_images' => 'Only Images',
            'show_images' => 'Show Images',
            'show_images_in_view' => 'Show Images in View',
            'downloadable' => 'Downloadable',
            'multiple_uploads_allowed' => 'Multiple Uploads Allowed',
            'preserve_filenames' => 'Preserve Filenames',
            'reorderable' => 'Reorderable',
            'editable_keys' => 'Editable Keys',
            'editable_values' => 'Editable Values',
            'several' => 'Several Selectable',
            'min_select' => 'Minimum Select',
            'min_select_helper' => 'Applies only when (Required)',
            'max_select' => 'Maximum Select',
            'max_select_helper' => '\'0\' means no limit',
            'auto_size' => 'Auto Size',
            'suggestions' => 'Suggestions',
            'add_suggestion' => 'Add Suggestion',
            'columns' => 'Columns',
            'toggle_grouped' => 'Toggle Grouped',
            'multiple_toggle_selectable' => 'Multiple Toggle Selectable',
            'alpine_mask' => 'Mask',
            'alpine_mask_help_text' => 'Quick help: a for letters, 9 for numbers * for all characters (Alpine mask)',
            'prioritized' => 'Prioritized',
            'prioritized_helper' => 'If the user can set a pure sequence of prioritized',
            'dynamic_prioritized' => 'Dynamically prioritized',
            'dynamic_prioritized_helper' => 'The individual selection fields appear step by step.',
        ],
    ],

    'form'=>[
        'custom_form' =>'Form',
        'short_title'=> 'Title',
        'custom_fields_amount'=> 'Number of Added Fields',
        'custom_form_identifier'=> [
            'display_name' => 'Form Type Name',
            'raw_name' => 'Form Type Identifier'
        ],
        'template'=> 'Template',
        'compiler' => [
            'custom_fields' => 'Custom Fields',
            'general_fields' => 'General Fields',
            'add_a_name_field' => 'Add a :name Field',
            'template_has_existing_fields' => 'The template contains existing fields',
            'template_has_existing_fields_description' => 'There are fields that originally come from this template. These fields are deleted from this form and the existing answers are adopted',
        ]
    ]
];
