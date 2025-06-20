<?php
// EN

return [
    'general_field' => [
        'label' => [
            'single' => 'General Field',
            'multiple' => 'General Fields',
        ],
        'attributes' => [
            'form_connections' => [
                'label' => 'Forms',
                'helper_text' => '',
            ],
            'name' => [
                'label' => 'Name',
                'helper_text' => '',
            ],
            'icon' => [
                'label' => 'Icon',
                'helper_text' => '',
            ],
            'type' => [
                'label' => 'Field Type',
                'helper_text' => 'The type of the field. Note: This cannot be changed after creation.',
            ],
            'identifier' => [
                'label' => 'Identifier Key',
                'helper_text' => 'This key is required to export the data.',
            ],
            'is_active' => [
                'label' => 'Active',
                'helper_text' => 'If deactivated, all general fields based on this field will also be deactivated.',
            ],
            'overwrite_options' => [
                'label' => 'Overwrite Settings',
                'message_on_create' => 'Create the general field to overwrite settings',
            ],
            'options' => [
                'label' => 'Settings',
                'message_on_create' => 'Create the general field to manage settings',
            ],
        ],
        'pages' => [
            'create' => [
                'title' => 'Create General Field',
            ],
            'edit' => [
                'title' => 'Edit General Field - :name',
            ],
            'list' => [
                'title' => 'General Fields',
            ],
        ],
        'navigation' => [
            'group' => 'Forms',
            'parent' => 'Forms',
        ],
        // Relations
        'form_connections' => [
            'label' => 'Linked Form Types',
        ],
    ],

    'general_field_form' => [
        'attributes' => [
            'custom_form_identifier_name' => [
                'label' => 'Form Type',
                'helper_text' => '',
            ],
            'export' => 'Exported',
            'is_required' => 'Required',
        ],
        'actions' => [
            'connect' => 'Link'
        ]
    ],

    'custom_field' => [
        'label' => [
            'single' => 'Field',
            'multiple' => 'Fields',
        ],
        'attributes' => [
            'is_active' => [
                'active' => 'Active',
                'not_active' => 'Inactive',
            ]
        ],
        'actions' => [
            'delete' => [
                'confirmation_message' => 'Delete the :type field ":name"?'
            ],
            'edit_options' => [
                'modal_heading' => 'Edit Field - :name'
            ],
            'dissolve' => [
                'label' => 'Dissolve',
                'tooltip' => '',
                'heading' => 'Are you sure you want to dissolve the template ":name"?'
            ]
        ]
    ],

    'custom_option' => [
        'label' => [
            'single' => 'Option',
            'multiple' => 'Options',
        ],
        'name' => [
            'label' => 'Name',
            'helper_text' => '',
        ],
        'identifier' => [
            'label' => 'Identifier',
            'helper_text' => '',
        ],
        'possible_options' => [
            'label' => 'Possible Options',
            'helper_text' => '',
        ],
    ],

    'rule' => [
        'label' => [
            'single' => 'Rule',
            'multiple' => 'Rules',
        ],
    ],

    'custom_form' => [
        'label' => [
            'single' => 'Form',
            'multiple' => 'Forms',
            'template' => 'Template',
            'templates' => 'Templates'
        ],
        'navigation' => [
            'group' => 'Forms',
            'parent' => '',
            'group-template' => 'Forms',
            'parent-template' => 'Forms',
        ],
        'attributes' => [
            'short_title' => 'Name',
            'custom_form_identifier' => 'Form Type',
            'template_identifier' => 'Template ID',
            'owned_fields_amount' => 'Field Count'
        ],
        'pages' => [
            'create' => [
                'title' => 'Create Form',
            ],
            'edit' => [
                'title' => 'Edit Form - :short_title',
            ],
            'list' => [
                'title' => 'Forms',
            ],
            'create_template' => [
                'title' => 'Create Template',
            ],
            'edit_template' => [
                'title' => 'Edit Template - :short_title',
            ],
            'list_template' => [
                'title' => 'Templates',
            ],
            'type_adder' => [
                'label' => 'Specific Fields',
                'new_field_name' => 'New Field'
            ],
            'general_field_adder' => [
                'label' => 'General Fields',
            ]
        ],
    ],

    'custom_form_answer' => [
        'label' => [
            'single' => 'Form Answer',
            'multiple' => 'Form Answers',
        ],
        'navigation' => [
            'group' => 'Forms',
            'parent' => 'Forms',
        ],
        'pages' => [
            'create' => [
                'title' => 'Fill Out Form',
            ],
            'edit' => [
                'title' => 'Edit Answers - :short_title',
            ],
            'view' => [
                'title' => 'Answers - :short_title',
            ],
            'list' => [
                'title' => 'Completed Forms',
            ]
        ],
        'attributes' => [
            'short_title' => 'Name'
        ]
    ]
];
