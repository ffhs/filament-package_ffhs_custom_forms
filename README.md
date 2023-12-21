
# Formulararten

## Erstellen einer neuen Formularart
### Schritt 1: Konfiguration erstellen
Um ein neue Formularart hinzuzufügen müssen Sie zuerst eine neue Klasse `DynamicFormConfiguration` hinzufügen. 
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function identifier(): string {  
	    return "test_form";  
	}  
	  
	public static function displayName(): string {  
	    return "Test Form";  
	}
}
```

- `identifier()`
	- Mit Identifier wird die Formularart den `CustomField`, `GeneralFieldForm` und `CustomForm` zugeordnet. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- `displayName()`
	- So wird die Formularart bei den `GeneralFields` und anderen Orten auf der Oberfläche gekennzeichnet.

### Schritt 2: Konfiguration registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
    "forms"=>[ 
	...
	CourseApplications::class,
	...
    ],  
    
    "custom_field_types" => [  
	...
    ],  
  
    "disabled_general_field_types"=>[  
	...
    ],  
  
    'view_modes' => [  
	...
    ]  
];
```


# CustomFieldType


## Neuer Feldtypen erstellen
### Schritt 1: Feldtyp Klasse erstellen

Um eine neue Feldtyp Klasse zu erstellen muss die neue Klasse von `CustomFieldType` erben

```php
class LocationSelectorType extends CustomFieldType  
{  

    public static function getFieldIdentifier(): string {  
        return "loc_selector";
    }  
    
     public function viewModes(): array {  
        return [
		'default'=>LocationSelectorTypeView::class,
		...
        ];
    }  
}
```
- `getFieldIdentifier()`
	- Mit Identifier wird die Formularart den `CustomField` und `GeneralField`. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- `viewModes()`
	- Die `viewModes` sind verschiedene Ansichten, diese können hier, in der globalen Konfigurationsdatei oder in den `DynamicFormConfiguration` ergänzt oder überschrieben werden
	- `default` **muss** vorhanden sein.

### Schritt 2: View erstellen
Die eine TypeView Klasse muss so aufgebaut sein und `FieldTypeView` implementieren:
```php
class LocationSelectorTypeView implements FieldTypeView  
{  
    public static function getFormComponent(CustomFieldType $type, CustomField $record,  
        array $parameter = []): \Filament\Forms\Components\Component {  
        return Select::make($record->identify_key)  
            ->label($type::class::getLabelName($record->customField))  
            ->helperText($type::class::getToolTips($record))  
            ->options([  
               1 => "Bern",  
               2 => "Zürich",  
               3 => "Basel",  
            ]);  
    }  
  
    public static function getViewComponent(CustomFieldType $type, CustomFieldAnswer $record,  
        array $parameter = []): \Filament\Infolists\Components\Component {  
        return TextEntry::make($record->customField->identify_key)  
            ->state($record->answare)  
            ->formatState(fn($state) => ([  
                1 => "Bern",  
                2 => "Zürich",  
                3 => "Basel",  
            ])[$state])  
            ->inlineLabel();  
    }  
}
```
- `getFormComponent()`
	- The Component for the edit Form
	- `CustomFieldType $type`: The type for them this view ist. 
		- It is for the primary functions like:
			- `getToolTips(CustomField $record)`
			- `getLabelName(CustomField $record)`
	- `CustomField $record`: The Record (Changing Soon)
	- `array $parameter`: Coming Soon
- `getViewComponent()`
	- The Component for the Infolist
	- `CustomFieldType $type`: The type for them this view ist. 
		- It is for the primary functions like:
			- `getToolTips(CustomField $record)`
			- `getLabelName(CustomField $record)`
	- `CustomFieldAnswer $record`: The Record
	- `array $parameter`: Coming Soon
### Schritt 3: Registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
    "forms"=>[ 
	...
    ],  
    
    "custom_field_types" => [  
	LocationSelectorType:class,
	...
    ],  
  
    "disabled_general_field_types"=>[  
	...
    ],  
  
    'view_modes' => [  
	...
    ]  
];
```


### Schritt 4: Translation
Das Feld benötigt noch einen übersetzen Namen

#### Variante 1 Default Language File
1. Erstelle/Öffne das Language File `.\lang\..\custom_forms`
2. Füge einen Eintrag bei `types.<fieldIdentifier>` hinzu
```php
return [
	'types'=>[
		'loc_selector' = 'Ort Selector'
	]
];
```

#### Variante 2  Überschreiben
1. Füge in Ihrer `CustomFieldType` die Methode `public function getTranslatedName(): string` hinzu
2. Setze den Rückgabeparameter auf den schon übersetzten Namen 
```php
class LocationSelectorType extends CustomFieldType  
{  
    public static function getFieldIdentifier(): string {  
        return "loc_selector";  
    }  
  
    public function viewModes(): array {  
        return [  
            'default'=>LocationSelectorTypeView::class,  
        ];  
    }  
  
    public function getTranslatedName(): string {  
        return __("my.location.is.this");  
    }  
}
```


## Feldtypen begrenzen für bestimmte Formulararten 

Es ist möglich die Feldtypen für eine bestimmte Formularart anzupassen. Standartmässig werden alle registrieren `CustomFieldType` 
1. Gehe in die entsprechende `DynamicFormConfiguration` 
2. Füge die Methode `formFieldTypes(): array` hinzu
3. Gebe die ausgewählten `CustomFieldType` an
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function identifier(): string {  
	    return "test_form";  
	}  
	  
	public static function displayName(): string {  
	    return "Test Form";  
	}

	public static function formFieldTypes(): array {  
	    return [  
	      TextType::class,  
	      DateTimeType::class,  
	      EmailType::class,
	      ...  
	    ];  
	}
}
```


## Feldtypen begrenzen für generelle Felder

Es ist Möglich die Feldtypen für die `GeneralFields` zu filtern. 
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die nicht erlaubten Feldtypen unter `disabled_general_field_types` hinzu
```php
return [  
    "custom_field_types" => [  
	    ...
    ],  
    "forms"=>[  
	...
    ],  
  
    "disabled_general_field_types"=>[  
        CheckboxType::class,  
        ...
    ],  

    'view_modes' => [  
	...  
    ],
  
];
```


# ViewModes

## Erklärung
Für jede `CustomFieldType` hat einen Aufbau für das Formular und die Ansicht. Nun kann es auch vorkommen, dass das Formular zwei verschiedene Ansichten braucht, dafür kann  man einen  `ViewMode` hinzufügen.
Der `ViewMode` kann an drei Orten registriert und überschrieben werden
- Im `CustomFieldType` selbst (Level 0)
- In der Plugin-Konfigurationsdatei (Level 1)
- Und in der `DynamicFormConfiguration` (Level 2)
Wenn die View in der Plugin-Konfigurationsdatei überschreiben wird, wird diese verwendet. Wenn sie zusätzlich oder in der `DynamicFormConfiguration` überschreiben wird gilt die `ViewMode` von der `DynamicFormConfiguration` .

Die `ViewMode` besteht aus einem `Key` und einem `FieldTypeView`

## Erstellen einer neuen TypeView
Die eine TypeView Klasse muss so aufgebaut sein und `FieldTypeView` implementieren:
```php
class LocationSelectorTypeView implements FieldTypeView  
{  
    public static function getFormComponent(CustomFieldType $type, CustomField $record,  
        array $parameter = []): \Filament\Forms\Components\Component {  
        return Select::make($record->identify_key)  
            ->label($type::class::getLabelName($record->customField))  
            ->helperText($type::class::getToolTips($record))  
            ->options([  
               1 => "Bern",  
               2 => "Zürich",  
               3 => "Basel",  
            ]);  
    }  
  
    public static function getViewComponent(CustomFieldType $type, CustomFieldAnswer $record,  
        array $parameter = []): \Filament\Infolists\Components\Component {  
        return TextEntry::make($record->customField->identify_key)  
            ->state($record->answare)  
            ->formatState(fn($state) => ([  
                1 => "Bern",  
                2 => "Zürich",  
                3 => "Basel",  
            ])[$state])  
            ->inlineLabel();  
    }  
}
```
- `getFormComponent()`
	- The Component for the edit Form
	- `CustomFieldType $type`: The type for them this view ist. 
		- It is for the primary functions like:
			- `getToolTips(CustomField $record)`
			- `getLabelName(CustomField $record)`
	- `CustomField $record`: The Record (Changing Soon)
	- `array $parameter`: Coming Soon
- `getViewComponent()`
	- The Component for the Infolist
	- `CustomFieldType $type`: The type for them this view ist. 
		- It is for the primary functions like:
			- `getToolTips(CustomField $record)`
			- `getLabelName(CustomField $record)`
	- `CustomFieldAnswer $record`: The Record
	- `array $parameter`: Coming Soon

## Hinzufügen einer `ViewMode` über `CustomFieldType`
Sehe **CustomFieldType -> Neuer Feldtypen erstellen -> Schritt 1**

## Überschreiben/Hinzufügen einer `ViewMode` über die Plugin-Konfigurationsdatei
Der Hinzufügen und überschreiben funktioniert genau gleich.
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge unter `view_modes` die `CustomFieldType` hinzu bei welcher eine `FieldView` hinzugefügt werden soll. 
3. Ergänze den `Key` (Namen) der `ViewMode` und die `FieldView` Klasse hinzu
```php
return [  
    "custom_field_types" => [  
	    ...
    ],  
    "forms"=>[  
	...
    ],  
  
    "disabled_general_field_types"=>[  
        ...
    ],  
    
    'view_modes' => [  
	TextType:class => [
		 "default"=>MyNewDefaultView::class, //Überschreiben
		 "super_view"=>MyNewSuperView::class, //Hinzufügen
		...
	],
	...  
    ],
  
];
```


## Überschreiben/Hinzufügen einer `ViewMode` über die `DynamicFormConfiguration`
1. Gehe in die entsprechende `DynamicFormConfiguration` 
2. Füge die Methode `getOverwriteViewModes(): array` hinzu
3. Gebe die ausgewählten `CustomFieldType` an
4. Füge im Array die `CustomFieldType` hinzu bei welcher eine `FieldView` hinzugefügt werden soll. 
3. Ergänze den `Key` (Namen) der `ViewMode` und die `FieldView` Klasse hinzu
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function identifier(): string {  
	    return "test_form";  
	}  
	  
	public static function displayName(): string {  
	    return "Test Form";  
	}

	public static function getOverwriteViewModes(): array {  
	    return [  
	      TextType::class =>>[
		      "default"=>MyNewDefaultView::class, //Überschreiben
		      "super_view"=>MyNewSuperView::class, //Hinzufügen
		      ...
	      ],
	      ...
	    ];  
	}
}
```


## Der `DynamicFormConfiguration` Default `ViewMode`
Es ist möglich die default `ViewMode` für:
- den `EditMode`
- den `ViewMode`
- den `CreateMode`
- den `displayMode` einstellen


</br>

1. Gehe in die entsprechende `DynamicFormConfiguration` 
2. Füge die Methode einer der folgenden Methoden hinzu:
	1. `displayViewMode():string`
	2. `displayEditMode():string`
	3. `displayCreateMode():string`
	4. `displayMode():string`
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function identifier(): string {  
	    return "test_form";  
	}  
	  
	public static function displayName(): string {  
	    return "Test Form";  
	}

	public static function displayViewMode(): array {  
	    return "super_view";
	}
}
```
