<?php
// DE

return [
    'events' => [
        'hidden_event' => ['label' => 'Feld verstecken'],
        'visible_event' => ['label' => 'Feld anzeigen'],
        'disabled_event' => ['label' => 'Feld deaktivieren'],
        'required_event' => ['label' => 'Feld benötigen'],
        'change_options_rule' => ['label' => 'Feld Optionen ändern'],
    ],
    'triggers' => [
        'value_equals_anchor' => [
            'label' => 'Bestimmter Wert',
            'field' => 'Feld',
            'bool' => [
                'label' => 'Boolean',
                'trigger_on_true' => 'Auslösen wenn der Wert Ja ist',
            ],
            'number' => [
                'label' => 'Nummer',
                'greater_smaller_info_on_empty' => 'Feld leer lassen, damit keine Abfrage ausgeführt wird',
                'smaller_than' => 'Kleiner als',
                'greater_than' => 'Grösser als',
                'exactly_number' => 'Genaue Nummer',
                'number' => 'Nummer',
            ],
            'options' => [
                'label' => 'Optionen',
                'selected_include_null' => 'Null inklusive',
                'selected_options' => 'Ausgewählte Optionen',
            ],
            'null' => [
                'label' => 'Leer',
            ],
            'text' => [
                'label' => 'Text',
            ],
        ],
        'infolist_view' => ['label' => 'Wenn Infolist ist'],
        'always' => ['label' => 'Immer Aktiv'],
    ],
];
