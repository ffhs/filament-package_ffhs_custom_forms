<?php
//DE

return [

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
    ],

    "fields" =>[
        'type' => 'Feldtyp',
        'name' => 'Name',
        'tool_tip'=> 'Kurzbeschreibung',
        'identify_key'=> 'Identifizierung Schlüssel',
        'is_general_field_active' => 'Aktive',
        'label'=> "Name",
        'form_connections'=> 'Verknüpfte Formulare',
        'general_field' => 'Generelles Felde',
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
            "module_select" => "Module Selector",
        ],
    ],

    'form'=>[
        'custom_form' =>'Formular',
        'short_title'=> 'Title',
        'custom_fields_amount'=> 'Anzahl der hinzugefügten Felder',
        'custom_form_identifier'=> [
            'display_name' => "Formulartype Name",
            'raw_name' => "Formulartype Identifier"
        ]
    ]

];
