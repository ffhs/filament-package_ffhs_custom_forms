<?php
//DE

return [
    'general_field' => [
        'label' => 'Generelles Feld',
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
            'message_on_create' => 'Erstellen Sie das Generaelle Feld, um die Einstelungen zu überschreiben',
        ],
        'options' => [
            'label' => 'Einstellungen',
            'message_on_create' => 'Erstellen Sie das Generaelle Feld, um die Einstelungen zu verwalten',
        ],

        //Relations
        'form_connections' => [
            'label' => 'Verknüpfte Formulartypen',
        ],
    ],

    'custom_field' => [
        'label' => [
            'single' => 'Feld',
            'multiple' => 'Felder',
        ],
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
        ],
    ]
];
