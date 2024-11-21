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
            'forms' => 'Formulare'
        ],
        'custom_form_answer' => 'Ausgefüllte Formulare',
        'templates' => 'Templates',
    ],

    'fields' => [
        'type' => 'Feldtyp',
        'name' => 'Name',
        'tool_tip' => 'Kurzbeschreibung',
        'identifier' => 'Identifikations Schlüssel',
        'is_general_field_active' => 'Aktiv',
        'label' => 'Name',
        'form_connections' => 'Verknüpfte Formulare',
        'general_field' => 'Generelles Feld',
        'is_required' => 'Wird benötigt',

        'helper_text' => [
            'type' => 'Der Feldtyp des Felds. ACHTUNG: Dieser kann nach dem Erstellen nicht mehr geändert werden',
            'identifier' => 'Dieser Schlüssel wird benötigt um die Daten zu exportieren',
            'is_general_field_active' => 'Falls dies deaktiviert wird, werden alle generellen Felder deaktiviert, welches auf diesem Feld basieren.',
        ],

        'type_view' =>[
            'select' => [
                'select' => 'Auswahl'
            ]
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
            'layout_text' => 'L. Text',
            'fieldset' => 'Fieldset',
            'tabs' => 'Tabs',
            'tab' => 'Tab',
            'wizard' => 'Schritt für Schritt',
            'wizard_step' => 'Schritt',
            'download_file' => 'Download',
            'image_layout' => 'L. Bild',
            'space' => 'Absatz',
            'file_upload' => 'Dokumente',
        ],

        'rules' => [
            'event' => [
                'hidden_event' => 'Feld verstecken',
                'visible_event' => 'Feld anzeigen',
                'disabled_event' => 'Feld deaktivieren',
                'required_event' => 'Feld benötigen',

                'change_options_rule' => 'Feld Optionen ändern'
            ],
            'trigger' => [
                'value_equals_anchor' => 'Bestimmter Wert',
                'infolist_view' => 'Wenn Infolist ist',
                'always' => 'Immer Aktive',
            ]
        ],

        'type_options' => [
            'boolean' => 'Ja/Nein Feld',
            'columns_count' => 'Anzahl Spalten',
            'column_span' => 'Zeilenweite',
            'icon' => 'Icon',
            'inline_label' => 'Titel in der Zeile',
            'inline' => 'In einer Zeile',
            'max_length' => 'Maximale Länge',
            'max_value' => 'Maximaler Wert',
            'min_length' => 'Minimale Länge',
            'min_value' => 'Minimaler Wert',
            'new_line' => 'Neue Zeile',
            'show_as_fieldset' => 'Als Fieldset beim Betrachten anzeigen',
            'show_in_view' => 'Sichtbar beim Betrachten',
            'show_title' => 'Titel Anzeigen',
            'required' => 'Benötigt',
            'help_text' => 'Hilfetext',

            // custom field type (FastTypeOption)
            'color_type' => 'Farbformat',
            'format' => 'Format',
            'only_images' => 'Nur Bilder',
            'show_images' => 'Bilder anzeigen',
            'show_images_in_view' => 'Bilder anzeigen in der Ansicht',
            'downloadable' => 'Herunterladbar',
            'multiple_uploads_allowed' => 'mehrere hochladbar',
            'preserve_filenames' => 'Ursprungsname erhalten',
            'reorderable' => 'Kann umgeordnet werden',
            'editable_keys' => 'Bearbeitbare Schlüssel',
            'editable_values' => 'Bearbeitbare Werte',
            'several' => 'Mehrere auswählbar',
            'min_select' => 'Mindestanzahl',
            'min_select_helper' => 'Greift nur bei (Benötigt)',
            'max_select' => 'Maximalanzahl',
            'max_select_helper' => '\'0\' entspricht keiner Begrenzung',
            'auto_size' => 'Automatische Grösse',
            'suggestions' => 'Vorschläge',
            'add_suggestion' => 'Vorschlag hinzufügen',
            'columns' => 'Spalten',
            'toggle_grouped' => 'Schalter gruppiert',
            'multiple_toggle_selectable' => 'Mehre Schalter auswählbar',
            'alpine_mask' => 'Maske',
            'alpine_mask_help_text' => 'Schnellhilfe: \'a\' für Buchstaben, \'9\' für Zahlen \'*\' für alle Zeichen. (Alpine Maske)',
            'grid_layout' => 'Dateien in einem Grid anzeigen lassen',
            'open_in_new_tab' => 'Files können in einem veuen Tab geöffnet werden',
            'prioritized' => 'Priorisiert',
            'prioritized_helper' => 'Der User kann die Reihenfolge der Optionen wählen.',
            'dynamic_prioritized' => 'Dynamisch priorisiert',
            'dynamic_prioritized_helper' => 'Die einzelnen Auswahlfelder erscheinen schrittweise.'

        ],
    ],

    'form' => [
        'custom_form' => 'Formular',
        'short_title' => 'Title',
        'custom_fields_amount' => 'Anzahl der hinzugefügten Felder',
        'custom_form_identifier' => [
            'display_name' => 'Formulartype Name',
            'raw_name' => 'Formulartype Identifier'
        ],
        'template' => 'Template',
        'compiler' => [
            'custom_fields' => 'Spezifische Felder',
            'general_fields' => 'Generelle Felder',
            'add_a_name_field' => 'Hinzufügen eines :name Feldes',
            'template_has_existing_fields' => 'Es gibt Felder die ursprünglich von diesem Template stammen',
            'template_has_existing_fields_description' => 'Es gibt Felder die ursprünglich von diesem Template stammen. Diese Felder werden von
                            diesem Formular gelöscht und die existierenden Antworten übernommen',
        ]
    ],
];
