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
//        'name' => 'Name',
        'tool_tip' => 'Kurzbeschreibung',
        'identifier' => 'Identifikations Schlüssel',
//        'is_general_field_active' => 'Aktiv',
//        'label' => 'Name',
        'form_connections' => 'Verknüpfte Formulare',
        'general_field' => 'Generelles Feld',
        'is_required' => 'Wird benötigt',

        'helper_text' => [
            'type' => 'Der Feldtyp des Felds. ACHTUNG: Dieser kann nach dem Erstellen nicht mehr geändert werden',
            'identifier' => 'Dieser Schlüssel wird benötigt um die Daten zu exportieren',
            'is_general_field_active' => 'Falls dies deaktiviert wird, werden alle generellen Felder deaktiviert, welches auf diesem Feld basieren.',
        ],

        'type_view' => [
            'select' => [
                'select' => 'Auswahl',
            ],
        ],

        'types' => [
            'text' => 'Text',
            'email' => 'Email',
            'number' => 'Nummer',
            'select' => 'Auswahl',
            'checkbox' => 'Kästchen',
            'section' => 'Sektion',
            'radio' => 'Radio',
            'date' => 'Datum',
            'date-time' => 'Datum/Zeit',
            'textarea' => 'Textbereich',
            'icon-select' => 'Icon',
            'checkbox_list' => 'Kästchen',
            'toggle_buttons' => 'Taste',
            'tags_input' => 'Etiketten',
            'color_input' => 'Farbe',
            'key_value' => 'Wertepaar',
            'title' => 'Titel',
            'layout_text' => 'Text',
            'fieldset' => 'Fieldset',
            'tabs' => 'Tabs',
            'tab' => 'Tab',
            'wizard' => 'Schritt für Schritt',
            'wizard_step' => 'Schritt',
            'download_file' => 'Download',
            'image_layout' => 'Bild',
            'space' => 'Absatz',
            'file_upload' => 'Dokumente',
            'repeater' => 'Repeater',
            'date_range' => 'Bereich',
        ],

        'rules' => [
            'event' => [
                'hidden_event' => 'Feld verstecken',
                'visible_event' => 'Feld anzeigen',
                'disabled_event' => 'Feld deaktivieren',
                'required_event' => 'Feld benötigen',

                'change_options_rule' => 'Feld Optionen ändern',
            ],
            'trigger' => [
                'value_equals_anchor' => 'Bestimmter Wert',
                'infolist_view' => 'Wenn Infolist ist',
                'always' => 'Immer Aktive',
            ],
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
