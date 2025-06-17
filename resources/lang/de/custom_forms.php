<?php
//DE

return [


    'functions' => [
        'connect' => 'Verknüpfen',
        'add' => 'Hinzufügen',
    ],

    'navigation' => [
        'general_fields' => 'Generelle Felder',
        'forms' => 'Formulare',
        'group' => [
            'forms' => 'Formulare',
        ],
        'custom_form_answer' => 'Ausgefüllte Formulare',
        'templates' => 'Templates',
    ],

    'fields' => [
        'type' => 'Feldtyp',
        'name' => 'Feld',
        'name_multiple' => 'Felder',
        'tool_tip' => 'Kurzbeschreibung',
        'identifier' => 'Identifikations Schlüssel',
//        'is_general_field_active' => 'Aktiv',
//        'label' => 'Name',
        'form_connections' => 'Verknüpfte Formulare',
        'general_field' => 'Generelles Feld',
//        'is_required' => 'Wird benötigt',

        'helper_text' => [
            'type' => 'Der Feldtyp des Felds. ACHTUNG: Dieser kann nach dem Erstellen nicht mehr geändert werden',
            'identifier' => 'Dieser Schlüssel wird benötigt um die Daten zu exportieren',
            'is_general_field_active' => 'Falls dies deaktiviert wird, werden alle generellen Felder deaktiviert, welches auf diesem Feld basieren.',
        ],
    ],

    'form' => [
        'custom_form' => 'Formular',
        'short_title' => 'Title',
        'custom_fields_amount' => 'Anzahl Felder',
        'owned_fields_amount' => 'Eigene Felder',
        'custom_form_identifier' => [
            'display_name' => 'Formulartype Name',
            'raw_name' => 'Formulartype Identifier',
        ],
        'template' => 'Template',
        'compiler' => [
            'custom_fields' => 'Spezifische Felder',
            'general_fields' => 'Generelle Felder',
            'add_a_name_field' => 'Hinzufügen eines :name Feldes',
            'template_has_existing_fields' => 'Es gibt Felder die ursprünglich von diesem Template stammen',
            'template_has_existing_fields_description' => 'Es gibt Felder die ursprünglich von diesem Template stammen. Diese Felder werden von
                            diesem Formular gelöscht und die existierenden Antworten übernommen',
        ],
    ],
];
