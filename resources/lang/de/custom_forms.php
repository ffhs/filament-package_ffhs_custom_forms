<?php
//DE

return [

    "navigation"=>[
        'general_fields'=> "Generelle Felder",
        'forms' => 'Formulare',
        'group' => [
            'forms'=> 'Formulare'
        ]
    ],

    "fields" =>[
        'type' => 'Feldtyp',
        'name' => 'Name',
        'tool_tip'=> 'Kurzbeschrieb',
        'identify_key'=> 'Identifizierung Schlüssel',
        'is_general_field_active' => 'Aktive',
        'label'=> "Name",
        'form_connections'=> 'Verknüpfte Formulare',

        'helper_text' => [
            'type'=> 'Der Feldtyp des Felds',
            'identify_key'=> 'Dieser Schlüssel wird benötigt um für die Daten zu exportieren',
            'is_general_field_active'=> 'Falls dies Deaktiviert wird, werden alle generelle Felder Deaktiviert, welches auf dieses Feld basieren.',
        ],

        'types'=>[
            "text" => "Text",
            "email" => "Email",
            "number" => "Nummer",
            "select" => "Auswahl",
            "checkbox" => "Kontrollkästchen",
            "radio" => "Radio",
            "date" => "Datum",
            "date-time" => "Datum und Zeit",
            "textarea" => "Textbereich",
            "module_select" => "Module Selector",
        ],
    ],

    'form'=>[
        'custom_form_identifier'=> [
            'display_name' => "Formulartype Name",
            'raw_name' => "Formulartype Identifier"
        ]
    ]

];
