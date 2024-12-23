<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\HideEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events\VisibleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger\ValueEqualsRuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;


trait FormConverterCase
{

    /**
     * @return void
     */
    public function setUpTestImportedSchema(): void
    {
        $this->testForm = new CustomForm([
            'short_title' => 'My custom form title',
            'custom_form_identifier' => 'test_form_identifier',
        ]);

        $this->testTemplate = new CustomForm([
            'id' => 2,
            'short_title' => 'My custom template title',
            'template_identifier' => 'my_template',
            'custom_form_identifier' => 'test_form_identifier',
        ]);
        $this->testGeneral = new GeneralField([
            'id' => 1,
            'name' => ['de' => 'test generales Feld', 'en' => 'test general field'],//['de' => 'test generales Feld', 'en' => 'test general field'],
            'identifier' => 'test_general',
            'type' => TextType::identifier(),
            'icon' => 'test-icon',
            'options' => ['required' => true],
        ]);


        $this->testFields = collect([
            new CustomField([
                'identifier' => '01',
                'name' => ['de' => 'Feld 1', 'end' => 'field 1'],
                'type' => SectionType::identifier(),
                'form_position' => 1,
                'layout_end_position' => 2,
            ]),
            new CustomField([
                'identifier' => '02',
                'name' => ['de' => 'Feld 2', 'end' => 'field 2'],
                'options' => ['required' => true],
                'type' => TextType::identifier(),
                'form_position' => 2,
            ]),

            (new CustomField([
                'identifier' => '03',
                'template_id' => 2,
                'form_position' => 3,
            ]))->setRelation('template', $this->testTemplate),

            (new CustomField([
                'general_field_id' => 1,
                'form_position' => 4,
            ]))->setRelation('generalField', $this->testGeneral),

        ]);


        $this->testTriggers1 = collect([
            new RuleTrigger([
                'is_inverted' => false,
                'type' => ValueEqualsRuleTrigger::identifier(),
                'data' => ['target' => '02', 'value' => '01']
            ]),
            new RuleTrigger([
                'is_inverted' => false,
                'type' => ValueEqualsRuleTrigger::identifier(),
                'data' => ['target' => '02', 'value' => '02']
            ])
        ]);

        $this->testTriggers2 = collect([
            new RuleTrigger([
                'is_inverted' => true,
                'type' => ValueEqualsRuleTrigger::identifier(),
                'data' => ['target' => '02']
            ])
        ]);

        $this->testEvents1 = collect([
            new RuleEvent([
                'type' => HideEvent::identifier(),
                'data' => ['target' => '01']
            ])
        ]);
        $this->testEvents2 = collect([
            new RuleEvent([
                'type' => VisibleEvent::identifier(),
                'data' => ['target' => '01']
            ])
        ]);


        $this->testRules = collect([
            (new Rule(['is_or_mode' => true]))
                ->setRelation('ruleTriggers', $this->testTriggers1)
                ->setRelation('ruleEvents', $this->testEvents1),
            (new Rule(['is_or_mode' => false]))
                ->setRelation('ruleTriggers', $this->testTriggers2)
                ->setRelation('ruleEvents', $this->testEvents2),
        ]);


        $this->testForm->setRelation('ownedFields', $this->testFields);
        $this->testForm->setRelation('ownedRules', $this->testRules);


        $this->testTemplate->setRelation('ownedFields', $this->testFields);
        $this->testTemplate->setRelation('ownedRules', $this->testRules);
    }

    protected function initialize(): void
    {


        $this->exportedFormInformations = [
            'short_title' => 'My custom form title',
        ];
        $this->exportedTemplateInformations = [
            'short_title' => 'My custom template title',
            'template_identifier' => 'my_template',
        ];

        $this->expordetFieldInformations = [
            [
                'identifier' => '01',
                'name' => ['de' => 'Feld 1', 'end' => 'field 1'],
                'type' => SectionType::identifier(),
                'fields' => [
                    [
                        'identifier' => '02',
                        'name' => ['de' => 'Feld 2', 'end' => 'field 2'],
                        'options' => ['required' => true],
                        'type' => TextType::identifier(),
                    ]
                ]
            ],
            [
                'identifier' => '03',
                'template' => 'my_template',
            ],
            [
                'general_field' => 'test_general',
            ]
        ];


        $this->expordetRuleInformations = [
            [
                'is_or_mode' => true,
                'triggers' => [
                    [
                        'is_inverted' => false,
                        'type' => ValueEqualsRuleTrigger::identifier(),
                        'data' => ['target' => '02', 'value' => '01']
                    ],
                    [
                        'is_inverted' => false,
                        'type' => ValueEqualsRuleTrigger::identifier(),
                        'data' => ['target' => '02', 'value' => '02']
                    ]
                ],
                'events' => [
                    [
                        'type' => HideEvent::identifier(),
                        'data' => ['target' => '01']
                    ]
                ]

            ],
            [
                'is_or_mode' => false,
                'triggers' => [
                    [
                        'is_inverted' => true,
                        'type' => ValueEqualsRuleTrigger::identifier(),
                        'data' => ['target' => '02']
                    ]
                ],
                'events' => [
                    [
                        'type' => VisibleEvent::identifier(),
                        'data' => ['target' => '01']
                    ]
                ]
            ]
        ];


        $this->expordetFlattenFieldInformations = [
            [
                "identifier" => "02",
                "name" => [
                    "de" => "Feld 2",
                    "end" => "field 2"
                ],
                "options" => [
                    "required" => true
                ],
                "type" => "text",
                "form_position" => 2,
            ],
            [
                "identifier" => "01",
                "name" => [
                    "de" => "Feld 1",
                    "end" => "field 1"
                ],
                "type" => "section",
                "form_position" => 1,
                "layout_end_position" => 4
            ],
            [
                "identifier" => "03",
                "form_position" => 4,
            ],
            [
                "form_position" => 5,
            ]
        ];

    }


}
