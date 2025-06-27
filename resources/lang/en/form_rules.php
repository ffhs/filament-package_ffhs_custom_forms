<?php
// EN

return [
    'events' => [
        'hidden_event' => ['label' => 'Hide field'],
        'visible_event' => ['label' => 'Show field'],
        'disabled_event' => ['label' => 'Disable field'],
        'required_event' => ['label' => 'Require field'],
        'change_options_rule' => ['label' => 'Change field options'],
    ],
    'triggers' => [
        'value_equals_anchor' => [
            'label' => 'Specific value',
            'field' => 'Field',
            'bool' => [
                'label' => 'Boolean',
                'trigger_on_true' => 'Trigger when the value is Yes',
            ],
            'number' => [
                'label' => 'Number',
                'greater_smaller_info_on_empty' => 'Leave field empty to skip condition',
                'smaller_than' => 'Smaller than',
                'greater_than' => 'Greater than',
                'exactly_number' => 'Exact number',
                'number' => 'Number',
            ],
            'options' => [
                'label' => 'Options',
                'selected_include_null' => 'Include null',
                'selected_options' => 'Selected options',
            ],
            'null' => [
                'label' => 'Empty',
            ],
            'text' => [
                'label' => 'Text',
            ],
        ],
        'infolist_view' => ['label' => 'When in Infolist view'],
        'always' => ['label' => 'Always active'],
    ],
];
