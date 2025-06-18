<?php
//DE

return [
    'general_field' => [
        'label' => [
            'single' => 'Generelles Feld',
            'multiple' => 'Generelle Felder',
        ],
        'attributes' => [
            'name' => [
                'label' => 'Name',
                'helper_text' => '',
            ],
            'icon' => [
                'label' => 'Icon',
                'helper_text' => '',
            ],
            'type' => [
                'label' => 'Feldtyp',
                'helper_text' => 'Der Feldtyp des Feldes. Hinweis: Dieser kann nach dem Erstellen nicht mehr geändert werden.',
            ],
            'identifier' => [
                'label' => 'Identifikationsschlüssel',
                'helper_text' => 'Dieser Schlüssel wird benötigt, um die Daten zu exportieren.',
            ],
            'is_active' => [
                'label' => 'Aktiv',
                'helper_text' => 'Wenn dies deaktiviert wird, werden alle allgemeinen Felder, die auf diesem Feld basieren, ebenfalls deaktiviert.',
            ],
            'overwrite_options' => [
                'label' => 'Einstellungen zum Überschreiben',
                'message_on_create' => 'Erstellen Sie das generelle Feld, um die Einstellungen zu überschreiben',
            ],
            'options' => [
                'label' => 'Einstellungen',
                'message_on_create' => 'Erstellen Sie das generelle Feld, um die Einstellungen zu verwalten',
            ],
        ],
        'pages' => [
            'create' => [
                'title' => 'Generelles Feld erstellen',
            ],
            'edit' => [
                'title' => ':name - Generelles Feld bearbeiten',
            ],
            'list' => [
                'title' => 'Generelles Feld',
            ],
        ],

        'navigation' => [
            'group' => 'Formulare',
            'parent' => 'Formulare',
        ],

        //Relations
        'form_connections' => [
            'label' => 'Verknüpfte Formulartypen',
        ],
    ],
    'general_field_form' => [
        'attributes' => [
            'custom_form_identifier_name' => 'Formularart',
            'export' => 'Wird exportiert',
            'is_required' => 'Wird benötigt',
        ],
        'actions' => [
            'connect' => 'Verknüpfen'
        ]
    ],
    'custom_field' => [
        'label' => [
            'single' => 'Feld',
            'multiple' => 'Felder',
        ],
        'attributes' => [
            'is_active' => [
                'active' => 'Aktive',
                'not_active' => 'Nicht aktiv',
            ]
        ],
        'actions' => [
            'delete' => [
                'confirmation_message' => 'Löschen des :type Feldes :name" '
            ],
            'edit_options' => [
                'modal_heading' => ':name - Feld bearbeiten'
            ],
            'dissolve' => [
                'label' => 'Auflösen',
                'tooltip' => '',
                'heading' => 'Möchten sie wirklich das Template \':name\' auflösen?'
            ]
        ]
    ],
    'custom_option' => [
        'label' => [
            'single' => 'Auswahlmöglichkeit',
            'multiple' => 'Auswahlmöglichkeiten',
        ],
        'name' => [
            'label' => 'Name',
            'helper_text' => '',
        ],
        'identifier' => [
            'label' => 'Identifikator',
            'helper_text' => '',
        ],
        'possible_options' => [
            'label' => 'Mögliche Auswahlmöglichkeiten',
            'helper_text' => '',
        ],
    ],
    'rule' => [
        'label' => [
            'single' => 'Regel',
            'multiple' => 'Regeln',
        ],
    ],
    'custom_form' => [
        'label' => [
            'single' => 'Formular',
            'multiple' => 'Formulare',
            'template' => 'Template',
            'templates' => 'Templates'
        ],

        'navigation' => [
            'group' => 'Formulare',
            'parent' => '',
            'group-template' => 'Formulare',
            'parent-template' => 'Formulare',
        ],

        'attributes' => [
            'short_title' => 'Name',
            'custom_form_identifier' => 'Formularart'
        ],

        'pages' => [
            'create' => [
                'title' => 'Formular erstellen',
            ],
            'edit' => [
                'title' => ':short_title - Formular anpassen',
            ],
            'list' => [
                'title' => 'Formulare',
            ],
            'create_template' => [
                'title' => 'Template erstellen',
            ],
            'edit_template' => [
                'title' => ':short_title - Template anpassen',
            ],
            'list_template' => [
                'title' => 'Templates',
            ],

            'type_adder' => [
                'label' => 'Spezifische Felder',
                'new_field_name' => 'Neues Feld'
            ],
            'general_field_adder' => [
                'label' => 'Generelle felder',
            ]
        ],

    ],

    'custom_form_answer' => [
        'label' => [
            'single' => 'Formularantwort',
            'multiple' => 'Formularantworten',
        ],
        'navigation' => [
            'group' => 'Formulare',
            'parent' => 'Formulare',
        ],
        'pages' => [
            'create' => [
                'title' => 'Formulare ausfüllen',
            ],
            'edit' => [
                'title' => ':short_title - Antworten anpassen',
            ],
            'view' => [
                'title' => ':short_title - Antworten',
            ],
            'list' => [
                'title' => 'Ausgefüllte Formulare',
            ]
        ],
        'attributes' => [
            'short_title' => 'Name'
        ]
    ]
];
