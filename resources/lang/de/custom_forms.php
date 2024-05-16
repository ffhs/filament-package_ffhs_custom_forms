<?php
//DE

return [

    'cache_duration'=> 1,

    'functions'=>[
        'connect'=>'Verknüpfen',
    ],

    "navigation"=>[
        'general_fields'=> "Generelle Felder",
        'forms' => 'Formulare',
        'group' => [
            'forms'=> 'Formulare'
        ],
        'custom_form_answer'=> "Ausgefüllte Formulare",
        "templates"=> "Templates"
    ],

    "fields" =>[
        'type' => 'Feldtyp',
        'name' => 'Name',
        'tool_tip'=> 'Kurzbeschreibung',
        'identify_key'=> 'Identifizierung Schlüssel',
        'is_general_field_active' => 'Aktive',
        'label'=> "Name",
        'form_connections'=> 'Verknüpfte Formulare',
        'general_field' => 'Generelles Feld',
        'is_required'=> 'Wird benötigt',

        'helper_text' => [
            'type'=> 'Der Feldtyp des Felds. ACHTUNG: Dieser kann nach dem erstellen nicht mehr geändert werden',
            'identify_key'=> 'Dieser Schlüssel wird benötigt um für die Daten zu exportieren',
            'is_general_field_active'=> 'Falls dies Deaktiviert wird, werden alle generelle Felder Deaktiviert, welches auf dieses Feld basieren.',
        ],

        'types'=>[
            "text" => "Text",
            "email" => "Email",
            "number" => "Nummer",
            "select" => "Auswahl",
            "checkbox" => "Kontrollkästchen",
            "section"=>"Sektion",
            "radio" => "Radio",
            "date" => "Datum",
            "date-time" => "Datum und Zeit",
            "textarea" => "Textbereich",
            "icon-select" => "Icon Auswahl",
            "checkbox_list" => "Kästchen Liste",
            "toggle_buttons"=> "Umschalttasten",
            'tags_input'=> 'Etiketten',
            'color_input'=>'Farbauswahl',
            'key_value'=> 'Schlüsselpaar',
            'title' => 'Titel',
            'layout_text'=>'Layout Text',
            'fieldset' => 'Fieldset',
            'tabs' => 'Tabs',
            'tab' => 'Tab',
            'wizard' => 'Schritt für Schritt',
            'wizard_step' => 'Schritt',
            'download_file' => 'Download Feld',
            'image_layout' => 'Bild im Layout',
            'space' => 'Absatz',
        ],

        'rules'=>[
            "is_disabled_rule" => "Feld deaktivieren",
            "is_hidden_rule" => "Feld verstecken",
            "is_required_rule" => "Feld benötigen",
            'change_options_rule' => 'Feld Optionen ändern'
        ],

        'anchors'=>[
            'value_equals_anchor' => 'Bestimmter Wert'
        ]
    ],

    'form'=>[
        'custom_form' =>'Formular',
        'short_title'=> 'Title',
        'custom_fields_amount'=> 'Anzahl der hinzugefügten Felder',
        'custom_form_identifier'=> [
            'display_name' => "Formulartype Name",
            'raw_name' => "Formulartype Identifier"
        ],
        'template'=> "Template"
    ]

];
