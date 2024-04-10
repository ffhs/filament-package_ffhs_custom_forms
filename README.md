# 00 Installation
## Registrieren des PAcketes in AdminPanelProvider
Fügen Sie das Plugin zu einem Panel hinzu, indem Sie die Plugin-Klasse instanziieren (\app\Providers\Filament\AdminPanelProvider.php) und sie an die plugin()-Methode der Konfiguration übergeben:
```php  
use Ffhs\FilamentPackageFfhsCustomForms\CustomFormPlugin;  
  
public function panel(Panel $panel): Panel  
{  
    return $panel
	     ... 
	    ->plugin([CustomFormPlugin::make()]);}        
	    ...          
```  

## Starten des Installers
Starten Sie den Installer
```bash  
php artisan filament-package_ffhs_custom_forms:install
```

<br>

# 01 Formulararten (DynamicFormConfiguration)


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
      </br>

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
];
```
</br>

## Konfigurations Möglichkeiten

### displayModes
Die `displayModes` bzw. `ViewModes` sind für die Darstellung der FeldTypen verantwortlich (Sehe ViewModes)

- `public static function displayViewMode():string`
    - Standard ViewMode
    - Wenn die anderen `displayModes` nicht geändert werden wird dieser Verwendet
- `public static function displayEditMode():string`
    - Standard ViewMode beim Editieren des Formulars (Bearbeiten der Antworten)
- `public static function displayCreateMode():string`
    - Standard ViewMode beim Erstellen des Formulars (Erstellen der Antworten)
- `public static function overwriteViewModes():array`
    - Mit dem kann man vorhandene ViewModes für einzelne Feldtypen ändern oder diese nur für dieses Formular registeren
- `public static function ruleTypes(): array`
    - Ändern der zur verfügung stehenden `FieldRuleType`
    - `null` meint, dass die Typen nicht überschreiben werden
- `public static function anchorRuleTypes(): array`
    - Ändern der zur verfügung stehenden `FieldRuleAnchorType`
    - `null` meint, dass die Typen nicht überschreiben werden`
      </br>

### ViewModes
```php
public static function overwriteViewModes():array {
	return [
		TextType::class => [
			 "default"=>MyNewDefaultView::class, //Überschreiben
			 "super_view"=>MyNewSuperView::class, //Hinzufügen
			 ...
		],
	];
```
</br>

### Feld Typen
Es ist möglich die Feldtypen für eine bestimmte Formularart anzupassen. Standartmässig werden alle registrieren `CustomFieldType`
1. Gehe in die entsprechende `DynamicFormConfiguration`
2. Füge die Methode `formFieldTypes(): array` hinzu
3. Gebe die ausgewählten `CustomFieldType` an
```php
public static function formFieldTypes(): array {  
    return [  
      TextType::class,  
      DateTimeType::class,  
      EmailType::class,
      ...  
    ];  
}
```
</br>

### Regel Typen
Mit `public  static function ruleTypes():array` können die zur Verfügung gestellten Regeltypen begrenzt werden.
```php
public static function ruleTypes(): array{  
    return [
	    IsRequiredRuleType::class,  
	    IsHiddenRuleType::class,
    ]
}
```
</br>

### Regel Anker Typen
Mit `public  static function anchorRuleType():array` können die zur Verfügung gestellten Ankertypen begrenzt werden.
```php
public static function anchorRuleType(): array{  
    return [  
	    ValueEqualsRuleAnchor::class
    ]
}
```
</br>

# 02 CustomForm Möglichkeiten zur Einbindung ToDo

</br>

# 03 CustomFieldType
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

    public function icon(): String {  
	return  "carbon-select-window";  
     }
}
```
- `getFieldIdentifier()`
    - Mit Identifier wird die Formularart den `CustomField` und `GeneralField`. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- `viewModes()`
    - Die `viewModes` sind verschiedene Ansichten, diese können hier, in der globalen Konfigurationsdatei oder in den `DynamicFormConfiguration` ergänzt oder überschrieben werden
    - `default` **muss** vorhanden sein.
- `icon()`
    - Das Icon welches angezeigt wird, wenn das Formular zusammengestellt wird.
      </br>

### Schritt 2: View erstellen
Die eine TypeView Klasse muss so aufgebaut sein und `FieldTypeView` implementieren:
```php
public static function getFormComponent(CustomFieldType $type, CustomField $record,  
    array $parameter = []): \Filament\Forms\Components\Component {  
    return Select::make(FormMapper::getIdentifyKey($record))  
        ->label(FormMapper::getLabelName($record))  
        ->helperText(FormMapper::getToolTips($record))  
        ->options([  
           1 => "Bern",  
           2 => "Zürich",  
           3 => "Basel",  
        ]);  
}  
  
public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,  
    array $parameter = []): Component {  
    return TextEntry::make(FormMapper::getIdentifyKey($record))  
        ->state(FormMapper::getAnswer($record))  
        ->formatState(fn($state) => ([  
            1 => "Bern",  
            2 => "Zürich",  
            3 => "Basel",  
        ])[$state])  
        ->inlineLabel();  
}
```
- `getFormComponent()`
    - The Component for the edit Form
    - `CustomField $record`: Das Model
    - `array $parameter`: Zur zeit werden dort Zusatzdaten übermittelt bei CustomLayoutType's
- `getViewComponent()`
    - The Component for the Infolist
    - `CustomFieldType $type`: The type for them this view ist.
        - It is for the primary functions like:
            - `getToolTips(CustomField $record)`
            - `getLabelName(CustomField $record)`
    - `CustomFieldAnswer $record`: The Record
    - `array $parameter`: Coming Soon
      </br>

### Schritt 3: Typen registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
    'custom_field_types' => [
	LocationSelectorType:class,
	...
    ],  
      
     'selectable_field_types' => [  
	LocationSelectorType:class,
	...
     ],  
     
     'selectable_general_field_types'=>[ 
	LocationSelectorType:class,
	...
     ],  

];
```
- `custom_field_types`
    - Allgemeine Registration, hier müssen alle Type eingetragen werden
- `selectable_field_types`
    - Die hier eingetragenen Typen, können im Formular Editor hinzugefügt werden.
- `selectable_general_field_types``
    - Die hier eingetragenen Typen, können als Typen für die generellen Felder gebraucht werden.
      </br>

### Schritt 4: Translation
Der Feldtypen benötigt noch einen übersetzen Namen
</br>

#### Variante 1 Über das standard Language File
1. Erstelle/Öffne das Language File `.\lang\..\custom_forms`
2. Füge einen Eintrag bei `types.<fieldIdentifier>` hinzu
```php
return [
	'types'=>[
		'loc_selector' = 'Ort Selector'
	]
];
```
</br>

#### Variante 2  Überschreiben des Standards
1. Füge in Ihrer `CustomFieldType` die Methode `public function getTranslatedName(): string` hinzu
2. Setze den Rückgabeparameter auf den schon übersetzten Namen
```php
class LocationSelectorType extends CustomFieldType  
{  
...  
    public function getTranslatedName(): string {  
        return __("my.location.is.this");  
    }  
}
```
</br>

## FormMapper
Der `FormMapper` vereinfacht das Rauslesen der Daten aus dem `CustomField`
- `getToolTips(CustomField|CustomFieldAnswer $record)`
- `getLabelName(CustomField|CustomFieldAnswer  $record)`
- `getIdentifyKey(CustomField|CustomFieldAnswer  $record)`
- `getOptionParameter(CustomField|CustomFieldAnswer $record, string $option)`
    - Gibt den Wert der TypeOption zurück (falls nicht vorhanden wird der default Wert zurückgegeben)
- CustomOption's
    - `getAllCustomOptions(CustomField $record):Collection`
        - Gibt alle möglichen `CustomOptions` Optionen zurück
    - `getAvailableCustomOptions(CustomField|CustomFieldAnswer $record)`
        - Gibt die für dieses Feld Ausgewählten Optionen Zurück
          </br>

## Generelle Felder
### Feldtypen für generelle Felder

Es ist Möglich die Feldtypen für die `GeneralFields` zu setzen.
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die erlaubten Feldtypen unter `general_field_types` hinzu
   ACHTUNG: Zurzeit muss die erlaubten Felder auch unter der Config Punkt `custom_field_types` definiert sein
```php
return [  
	...
	"general_field_types"=>[  
	    CheckboxType::class,  
	    EmailType::class,  
	    NumberType::class,  
	    SelectType::class,  
	    ... 
	],  
	...
];
```
</br>

## Feldtyp mit extra Optionen
### Deaktivierbar | Erforderlichbar

```php
class LocationSelectorType extends CustomFieldType  
{  
	...
	public function canBeDeactivate():bool {  
	    return true;  
	}  
	public function canBeRequired():bool {  
	    return true;  
	}
}
```
- `canBeDeactivate()`
    - Falls `false` wird der Slider `Aktive` in den Feldoptionen nicht angezeigt (Immer Aktive)
- `canBeRequired()``
    -  Falls `false` wird der Slider `Benötigt` in den Feldoptionen nicht angezeigt (Immer Aus)

</br>

### Feldoptionen
In manchen Fällen benötigt man mehr Optionen. Diese können mit `TypeOption`'s hinzugefügt werden
</br>

#### TypeOption Class

```php
class MyOption extends TypeOption  
{  
    public function getDefaultValue(): mixed {  
        return "test";  
    }  
  
    public function getComponent(string $name): Component {  
       return TextInput::make($name)  
           ->label("MeineOption") 
           ->live();  
    }  
}
```
- `getDefaultValue(): mixed`
    - Standardwert dieser `TypeOption`
- `getComponent(string $name): Component`
    - Die Komponente dieser `TypeOption` welche Angezeigt wird
- Andere überschreibbare Methoden:
    - `mutateOnCreate(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim erstellen des `CustomField`'s
    - `mutateOnSave(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim speichern des `CustomField`'s
    - `mutateOnLoad(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim laden des `CustomField`'s
          </br>

#### Deklarieren in dem Feldtypen
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function getExtraTypeOptions(): array {
		return [
			"my_option_name" => new MyOption() // <= Standart
			'in_line_label' => (new InLineLabelOption())
				->modifyComponent(fn($toggle) => $toggle->columnStart(1)), //<= Bearbeiten einer Componente
			"my_fast_option" => new FastTypeOption(false,   //<= In File
			    Toggle::make("several")  
			        ->label("Mehre auswählbar")
			        ->columnSpanFull()  
			        ->live()  
			),
		];
	}
}
```
- `getExtraTypeOptions()`
    - Diese Optionen können beim Bearbeiten des `CustomField`s im Editor angepasst werden
- `modifyComponent(fn(Component $component) => ...)`
    - Diese Funktion bietet die Möglichkeit einer vordefinierte `TypeOption` die Komponenten anzupassen
- `FastTypeOption(mixed $defaultValue, Component $component)`
    - Um schnell eine `TypeOption` hinzuzufügen
      </br>

#### Benutzen einer TypeOption
```php
class LocationSelectorTypeView implements FieldTypeView  
{  
    public static function getFormComponent(CustomFieldType $type, CustomField $record,  array $parameter = []): \Filament\Forms\Components\Component { 
	return Select::make(FormMapper::getIdentifyKey($record))   
	        ->label(FormMapper::getLabelName($record))  
	        ->helperText(FormMapper::getToolTips($record))  
	        ->options([  
	           1 => FormMapper::getOptionParameter(record,"my_option_name"),   //<===============
	        ]);  
    }  
    ...
}
```
- Die `TypeOption` können am einfachsten mit dem `FormMapper` erreicht werden
    - `getOptionParameter(CustomField|CustomFieldAnswer $record, string $option)`
- Die roh Daten können über `$record->options` erreicht werden
  </br>

### TypeOption's im generellem Feld
```php
class LocationSelectorType extends CustomFieldType  
{  
	...
	public function getExtraGeneralTypeOptions(): array {
		return [
			"my_gen_option_name" => new MyOption()
		];
	}
	..
}
```
- `getExtraGeneralTypeOptions()`
    - Diese Optionen werden nur in den Generellen Felder angezeigt
      </br>

### Standard TypeOption's
```php
class LocationSelectorType extends CustomFieldType  
{  
	use HasBasicSettings;
	...
	public function extraOptionsBeforeBasic(): array {
		return [
			"my_gen_option_name" => new MyOption()
		];
	}
	..
}
```
- `HasBasicSettings` Bring folgende Optionen mit:
    - `column_span`
    - `in_line_label`
    - `new_line_option`
- Weitere Optionen können mit folgenden Methoden hinzugefügt werden:
    - `extraOptionsBeforeBasic()`
    - `extraOptionsAfterBasic()`
      </br>

## Feldtypen mit Auswahloptionen (CustomOption's)

```php
class LocationSelectorType extends CustomOptionType  
{  
	...
}
```
- `CustomOptionType`
    - Fügt die Funktionalitäten hinzu, welche verwendet werden, damit Felder  Auswahloptionen (`CustomOption`'s) verwenden können
        - Es fügt die Benötigte `TypeOptions` hinzu
        - Die Klasse verändert die mutations Funktionen
    - Um weitere `TypeOptions` hinzuzufügen müssen die neuen Optionen mit den Elternoptionen verbunden werden.  (Selbes gilt für `getExtraGeneralTypeOptions`)
```php
public function getExtraTypeOptions(): array {  
    return array_merge(  
        [
		"my_gen_option_name" => new MyOption()
        ],  
	parent::getExtraTypeOptions()
   );
}
```
</br>

```php
class LocationSelectorTypeView implements FieldTypeView  
{ 
   use HasCustomOptionInfoListView;  
  
   public static function getFormComponent(CustomFieldType $type, CustomField $record,  array $parameter = []): Component {  
  
       $select = Select::make($FormMapper::getIdentifyKey($record))  
        ->helperText($FormMapper::getToolTips($record))  
        ->label($FormMapper::getLabelName($record))  
        ->required($record->required)  
        ->options(FormMapper::getAvailableCustomOptions($record));  

       return $select;  
   }
    
}
```
- `HasCustomOptionInfoListView`
    - Fügt eine standard `getInfolistComponent` Methode hinzu. Diese kann Unterscheiden ob eine oder mehrere Optionen auswählbar sind, und stellt dies dementsprechend dar.
      </br>

## Layout-Felder (FeldLayoutType)

```php 
class SectionType extends CustomLayoutType  
{    
  
    public static function getFieldIdentifier(): string {  
        return "section";  
    }  
  
    public function viewModes(): array {  
        return [  
            "default" => SectionTypeView::class  
        ];  
    }  
    
    public function canBeRequired(): bool {  
        return false;  
    }  
  
    public function icon(): string {  
       return  "tabler-section";  
    }  
}
```
- `CustomLayoutType`
    -  Der `CustomLayoutType` wird beim Rendern des Formulars anders behandelt, er erhält bei der View als Parameter die Formularfelder, welche ihm untergeordnet sind.  
       </br>

```php 
class SectionTypeView implements FieldTypeView  
{  
  
    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {  
        return Section::make(FormMapper::getLabelName($record))  
            ->schema($parameter["rendered"]);  
    }  
  
    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {  
        return \Filament\Infolists\Components\Section::make(FormMapper::getLabelName($record))
        ->schema($parameter["rendered"]);  
    }  
  
}
```
- `$parameter["rendered"]`
    - In dem Parameter `"rendered"` sind die weiteren Formfelder bzw. die Infolistfelder drinnen.
- `$parameter["customFieldData"]`
    - In dem Parameter `"customFieldData"` sind die Rohdaten der `CustomField`'s enthalten.
      </br>

## Anpassen des Repeaters in dem Formular Editor

### Item-Label
- Das Item-Label kann mit `nameFormEditor` und `nameBeforeIconFormEditor` angepasst werden.
    - `$state` sind die Daten des `CustomFields`
- `nameFormEditor` Ist für den Namen und das nach dem Namen folgende angedacht
- `nameBeforeIconFormEditor` Ist für Badge's angedacht
```php 
class LocationSelectorType extends CustomFieldType   
{
	...
	public function nameFormEditor(array $state):string {  
	    return $state["name_de"] . " Something very special";  
	}  
	  
	public function nameBeforeIconFormEditor(array $state):string {  
	    $newBadge = new HtmlBadge("Neuer Badge", Color::rgb("rgb(34, 135, 0)"));
	    return parent::nameBeforeIconFormEditor() . $newBadge;  
	}
}
```

### Funktionen
- Die im Repeater angezeigten Item-Funktionen angepasst werden.
#### Aufbau
```php 
class LocationSelectorType extends CustomFieldType   
{
	public function repeaterFunctions(): array {
		return [
			RepeaterFieldAction::class => function(CustomForm $form, Get $get, array $state, array $arguments):bool {  
			    $customField = $state[$arguments["item"]];
			    return true;
			} 
		];
	}
}
```
- In den `RepeaterFieldAction` sind die Actions Gespeichert.
- Die Closure-Funktion gibt einen boolean zurück die entscheidet, ob die Action angezeigt werden soll. (ACHTUNG: Die Funktion gilt für jedes Feld und sit nicht beschränkt auf den Typen)

#### Standard Type Closure
- Wie anschliessend dargestellt, kann man die Action auf den aktuellen typen begrenzen, ohne eine eigene Methode zu schreiben
```php 
class LocationSelectorType extends CustomFieldType   
{
	public function repeaterFunctions(): array {
		return [
			EditAction::class => EditAction::getDefaultTypeClosure($this::getFieldIdentifier())
		];
	}
}
```

#### RepeaterFieldAction's
```php
class MyRepeaterFieldAction extends RepeaterFieldAction  
{  
  
	public function getAction(CustomForm $record, array $typeClosers): Action {  
	   return Action::make('edit')  
			->action(fn() => dd("somme action"))   
		     ->visible($this->isVisibleClosure($record,$typeClosers)) //<===============
	}  
}
```
- `array $typeClosers` Hier sind alle Closure-Funktionen hinterlegt.
- Am einfachsten benutzen Sie die vordefinierte `isVisibleClousure` Funktion um den benötigten Closure zu erhalten


## Andere Methoden zum Überschreiben
- `prepareSaveFieldData(mixed $data): ?array`
- `prepareLoadFieldData(array $data): mixed`
- `overwrittenRules():?array`
    - Überschreiben von den RuleType (Schaue __05 Regel und Anker__)
- `overwrittenAnchorRules():?array`
    - Überschreiben von den AnchorRuleType (Schaue __05 Regel und Anker__)
- `afterEditFieldSave(CustomField $field, array $rawData):void`
- `afterEditFieldDelete(CustomField $record):void`
- `afterAnswereFieldSave(CustomFieldAnswer $field, array $rawData, array $formData):void`
- `mutateOnTemplateDissolve(array $data):array`

# 04 ViewModes
## Erklärung
Für jede `CustomFieldType` hat einen Aufbau für das Formular und die Ansicht. Nun kann es auch vorkommen, dass das Formular zwei verschiedene Ansichten braucht, dafür kann  man einen  `ViewMode` hinzufügen.
Der `ViewMode` kann an drei Orten registriert und überschrieben werden
- Im `CustomFieldType` selbst (Level 0)
- In der Plugin-Konfigurationsdatei (Level 1)
- Und in der `DynamicFormConfiguration` (Level 2)
  Wenn die View in der Plugin-Konfigurationsdatei überschreiben wird, wird diese verwendet. Wenn sie zusätzlich oder in der `DynamicFormConfiguration` überschreiben wird gilt die `ViewMode` von der `DynamicFormConfiguration` .

Die `ViewMode` besteht aus einem `Key` und einem `FieldTypeView`
</br>

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
      </br>

## Hinzufügen einer `ViewMode` über `CustomFieldType`
Sehe **CustomFieldType -> Neuer Feldtypen erstellen -> Schritt 1**
</br>

## Überschreiben/Hinzufügen einer `ViewMode` über die Plugin-Konfigurationsdatei
Der Hinzufügen und überschreiben funktioniert genau gleich.
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge unter `view_modes` die `CustomFieldType` hinzu bei welcher eine `FieldView` hinzugefügt werden soll.
3. Ergänze den `Key` (Namen) der `ViewMode` und die `FieldView` Klasse hinzu
```php
return [  
	...    
	'view_modes' => [  
		TextType:class => [
			 "default"=>MyNewDefaultView::class, //Überschreiben
			 "super_view"=>MyNewSuperView::class, //Hinzufügen
			...
		],
		...  
	],
	...  
  
];
```

</br>

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

</br>

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
</br>

# 05 Regeln und Anker (FieldRule)
## Erklärung
Eine `FieldRule` besteht aus zwei Teilen.
- `FieldRuleAnchorType` entscheidet, wann die Regel ausgeführt wird
- `FieldRuleType` ist der Ausführendende Teil
  </br>

## FieldRuleAnchorType
### Schritt 1 Class deklarieren
```php
class MyRuleAnchorType extends FieldRuleAnchorType  
{  
    public static function identifier(): string {  
        return "value_equals_anchor";  
    }  
  
    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {  
        return TextInput:make("my_importent_value");
    }  
  
  
    public function shouldRuleExecute(array $formState, CustomField $customField, FieldRule $rule): bool {  
       return true; //to your decision
    }
```

- `identifier():string`
    - Mit Identifier wird die Formularart den `CustomField`, `GeneralFieldForm` und `CustomForm` zugeordnet. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- - `getCreateAnchorData():array`
    - Diese Methode soll, die standard Werte zurückgeben. Wieso? Weil in den Actions Modals nicht die Funktion `->default()` funktioniert und es kann zu Speicher-Problemen führen falls man keinen Standard setzt.
- `settingsComponent(CustomForm $customForm, array $fieldData): Component`
    - Diese Komponente wird, dem User angezeigt, beim Definieren dieser Regel. Die Informationen findet man danach bei `$fieldRule->anchor_data["my_importent_value"]`
- Andere Methoden zum überschreiben:
    - `canAddOnField(CustomFieldType $type):bool`
        - Kann an diesem Typen hinzugefügt werden.
    - `mutateDataBeforeLoadInEdit(array $ruleData, FieldRule $rule): array`
        - Verändern der Daten beim Laden in den Edit Mode (Dort wo das Formular bearbeitet werden kann)
    - `mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array`
        - Verändern der Daten beim Speichern in den Edit Mode (Dort wo das Formular bearbeitet werden kann)
    - `mutateRenderParameter(array $parameter, CustomField $customField, FieldRule $rule): array`
        - Verändere die Parameter welche den Felder beim Rendern mitgegeben werden
    - `mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):arra`
        - //ToDo
          </br>

### Schritt 2 Registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
	...
	"field_rule_anchor_types"=>[  
	    MyRuleAnchorType::class  
	],
];
```
</br>


## FieldRuleType
### Schritt 1 Class deklarieren
```php
class IsMyRuleType extends FieldRuleType  
{  
	public static function identifier(): string {  
		return "value_equals_rule";  
	}  

	public function getCreateRuleData(): array {  
	    return ["my_importent_value"=>"default"];  
	}

	public function settingsComponent(CustomForm $customForm, array $fieldData): Component {  
		return TextInput:make("my_importent_value");
	} 

	//Beispiel
	public function afterRender(Component|\Filament\Infolists\Components\Component $component ,CustomField $customField, FieldRule $rule): Component|\Filament\Infolists\Components\Component {  
		$anchorDecisions = $rule->getAnchorType()->canRuleExecute($component,$customField,$rule); // Bekomme die Entscheidung des Ankers
		//Erledige deine Sachen
	}
}
```

- `identifier():string`
    - Mit Identifier wird die Formularart den `CustomField`, `GeneralFieldForm` und `CustomForm` zugeordnet. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- `getCreateRuleData():array`
    - Diese Methode soll, die standard Werte zurückgeben. Wieso? Weil in den Actions Modals nicht die Funktion `->default()` funktioniert und es kann zu Speicher-Problemen führen falls man keinen Standard setzt.
- `settingsComponent(CustomForm $customForm, array $fieldData): Component`
    - Diese Komponente wird, dem User angezeigt, beim Definieren dieser Regel. Die Informationen findet man danach bei `$fieldRule->anchor_data["my_importent_value"]`
- Andere Methoden zum überschreiben:
    - `canAddOnField(CustomFieldType $type):bool`
        - Kann an diesem Typen hinzugefügt werden.
    - `mutateDataBeforeLoadInEdit(array $ruleData, FieldRule $rule): array`
        - Verändern der Daten beim Laden in den Edit Mode (Dort wo das Formular bearbeitet werden kann)
    - `beforeRender(CustomField $customField, FieldRule $rule)`
    - `afterRender(Component|InfoComponent $component, CustomField $customField, FieldRule $rule): Component|InfoComponent`
    - `mutateLoadAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed`
    - `mutateSaveAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed`
    - `afterAnswerSave( FieldRule $rule, CustomFieldAnswer $answer):void`
    - `mutateRenderParameter(array $parameter, CustomField $customField, FieldRule $rule): array`
        - Verändere die Parameter welche den Felder beim Rendern mitgegeben werden
    - `mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):arra`
        - //ToDo
          </br>

### Schritt 2 Registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
	...
	"field_rule_types"=>[  
	    IsMyRuleType::class  
	],
];
```
</br>

# 06 Benutzer und UI ToDo
# Formulare
##   Formulare Erstellen
##   Bearbeiten
##   Neues Formular zum ausfüllen
##   Ausfüllen
#  Generelle Felder
# Templates
# Regeln
