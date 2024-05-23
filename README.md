# 00 Installation
## 00.00 Hinterlegen des Packages im Composer
... //ToDo

```bash
composer update
``` 
<br>

## 00.01 Registrieren des Packetes in AdminPanelProvider
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
  <br>

## 00.02  Starten des Installers
Starten Sie den Installer
```bash  
php artisan filament-package_ffhs_custom_forms:install
```
<br>

## 00.03 Setup des Icon-Picker
- Das Custom Forms Plugin benötigt ein [Icon Picker Plugin](https://v2.filamentphp.com/plugins/icon-picker)
- Damit das Plugin funktioniert solte man die Config generieren
```bash
php artisan vendor:publish --tag="filament-icon-picker-config")
```

- Anschliessend empfehle ich folgendes Einzustellen
```php
return [  
	'sets' => null,  
	'columns' => 3,  
	'layout' => \Guava\FilamentIconPicker\Layout::FLOATING,  
	'cache' => [  
	    'enabled' => true,  
	    'duration' => '7 days',  
	],
]
``` 
<br>

# 01 Grundlagen zu den Formularen
## 01.00 Eigene Formulare (Basic zu Formulararten `DynamicFormConfiguration`)
- Im ersten überblick wollen wir ein Eigenes Formular hinzu fügen.
- Um ein Formular im UI zu erstellen wird eine Formulararten benötigt `DynamicFormConfiguration`
    -  `DynamicFormConfiguration` Werden verwendet um Formulare von einander zu unterscheiden und die Formulararten individual zu konfigurieren
    - Zu Jeder Formularart können beliebig viele Formulare erstelt werden.
    - Wofür kann man verschiedene Formulararten erstellen? Ein Beispiel: Wir programmieren ein BZM Dort gibt es Formulare zur Anmeldung für Studiengänge. Aber es gibt auch Formulare um zur überprüfen ob man für einen Studiengang geeignet ist. Da man beim Eignungsgesuch nicht die Selben Pflichtfelder benötigt, ist es dort möglich eine eigene Formularart zu verwenden.
      </br>

## 01.01 Ein Formularart erstellen
### 01.01.00 Ein Konfigurationsklasse erstellen
- Um ein neue Formularart hinzuzufügen müssen Sie zuerst eine neue Klasse erstellen, welche von `DynamicFormConfiguration` erbt.
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
    - Mit Identifier wird die Formularart den Formularfelder zugeordnet. Es ist empfohlen diesen im nachhinein **nicht** zu ändern!!!!
- `displayName()`
    - So wird die Formularart auf det UI-Oberfläche gekennzeichnet.
      </br>

### 01.01.00 Ein Formularart registrieren
1. Gehe Sie in die Config `ffhs_custom_forms.php`
2. Füge Sie die `DynamicFormConfiguration` bei `forms` hinzu
```php
return [  
    "forms"=>[ 
		CourseApplications::class,
    ],  
];
```
</br>

## 01.02 Ein Formular bearbeiten und Felder hinzufügen
### 01.02.01 Formular erstellen
- Gehen Sie auf die Weboberfläche und auf den Punkt `Formulare`
- Klicken Sie auf `Erstellen`
- Geben Sie unter `Namen` den Namen des Formulares an.
- Wählen Sie bei `Formularart` einen Formularart aus.
- Klicken Sie auf `Erstellen`
### 01.02.01 Felder hinzufügen
- Sie werden anschliessend auf eine Seite Weitergeleitet. Dort können Sie ihr Formular zusammensetzten.
- Wählen sie Dafür unter `Spezifische Felder` ein Feld aus und klicken Sie dort drauf.
    - Es wird sich ein Fenster öffnen
    - Links Oben:
        - Dort befinden sich einmal die Namens Felder und die Kurzbeschreibung
            - `Name`: Dieser wird später über dem Feld angezeigt
            - `Kurzbeschreibung`: Dieser wird als Tooltip angeizeigt fals das Feld nicht leer gelassen wird
    - Rechts Oben:
        - Dort befinden sich die Optionen des Feldes
        - Oberer Abschnitt:
            - `Aktive`: Falls Nein wird das Feld nicht angezeigt
            - `Benötigt`: Fragt ab ob das Feld ausgefüllt werden muss
        - Unterer Abschnitt:
            - Dort Befinden sich weiter Optionen die Sich von Feldtypen zu Feldtypen unterscheiden können
    - Unten:
        - Unten finden sie die `Regeln`, dazu finden sie in (//ToDo set the section) mehr
    - Anschliessend wenn alle Einstellungen getätigt, sind drücken sie auf `Absenden`, dann wird das Feld hinzugefügt **aber nicht gespeichert**
    - Klicken sie auf `Speichern` um das Formular zu speichern.


## 01.03 Formular Eigenschaften
// ToDo <br>

# 02 Formular Felder, Generelle Felder und Templates
## 02.00 Formularfelder und deren Aufbau
### 02.01.00 Das Wichtigste
- Ein Formularfeld ist ein Feld welches einem Formular zugeordnet werden kann.
- Ein Formularfeld wird im Code als `CustomField` bezeichnet
- Ein Formularfeld kann einem Formular so wie bei  **01.02.01** Gezeigt hinzu gefügt werden </br>

### 02.00.01 Eigenschaften
- Ein Formularfeld hat folgende wichtige Eigenschaften
    - `identify_key` => Dieser Key wird verwendet um die Antworten einem Feld zuzuordnen beim Export. Oder bei manchen Funktionalitäten, wie Beispielsweissen den Feld-Regeln (Sehe **08 Regeln**)
    - `form_position` => Diese hinterlegt wo das Feld im Formular liegt**
    - `type` => Hier wird der Feldtyp abgespeichert. Mehr bei **04 Feldtypen**
    - `custom_form_id` => Dies ist ein Foreignkey zu dem Formular
- Weitere Basiseigenschaften:
    - `name_de`
    - `name_en`
    - `required`
    - `is_active`
- Weitere Eigenschaften:
    - `options`
    - `template_id`
    - `general_field_id` </br>

##  02.01 Generelle Felder

### 02.01.00 Das Wichtigste
- Generelle Felder sind auf erste Line für den Export da.
- Diese Felder werden im Code als `GeneralField` bezeichnet
- Ein generelles Feld im Formular ist nicht ein generelles Feld, sondern ein `CustomField` welches auf ein `GneralField` zeigt über `general_field_id`
- Das `GeneralField` Model beinhaltet anschliessend alle wichtige Informationen für da `CustomField` wie Beispielsweise den Typen.
- Generelle Felder können zu Formulararten hinzugefügt werden. Im gleichen Schritt können Sie bestimmen ob das generelles Feld in dieser Formularart vorhanden sein muss und/oder ob dieses Expotiert wird.
- Pro Formular kann jedes generelle Feld maximal einmal hinzugefügt werden. </br>

### 02.01.01 Eigenschaften
-  Wichtige Eigenschaften:
   -  `identify_key => Dieser Key wird verwendet um die Felder später zu expotieren
   - `icon` => Das Icon wird verwendet, für das generellen Felder besser wieder zu erkennen im Formulareditor
   -  `type` => Hier wird der Feldtyp abgespeichert. Mehr bei **04 Feldtypen**
- Andere Eigenschaften
  - `is_active`
  - `tool_tip_de`
  - `tool_tip_en`
  - `name_de`
  - `name_en`
  - `extra_options` </br>

### 02.01.02 Weboberfläche // ToDo
#### 02.01.02.00 Generelles Feld erstellen
#### 02.01.02.01 Generelles Feld zu einer Formularart hinzufügen
#### 02.01.02.01 Generelles Feld in einem Formular hinzufügen
</br>

### 02.01.03 generelle Felder Feldtypenbegrenzung
Es ist Möglich die Feldtypen für die Auswahl in den `GeneralFields` zu setzen.
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

## 02.02 Templates
### 02.02.00 Das Wichtigste
- Templates sind dafür da, grössere Formular abschnitte für verschiedene Formulare bereitzustellen.
- Templates sind technisch gesehene nur ein Formular. Welche `is_template` auf `true` gesetzt haben
- Templates können **nicht** in Templates eingelegt werden
- Templates werden wie generelle Felder nicht direkt als `Template` eingefügt sondern als `CustomFields` mit einer Relation zu `CustomForm` über das `template_id` attribute
- `CustomFields` welche bei `template_id` einen wert gesetzt haben, haben automatisch den Template Feldtypen (`TemplateFieldType`)
- Templates können, wenn sie in einem Formular vorhanden sind aufgelöst werden. So das die Felder in das Formular übernommen werden. </br>

### 02.02.01 Besonderheiten im Hintergrund.
#### 02.02.01.00 Templates auflösen
##### 02.02.01.00.01 Gespeicherte Antworten
- Wenn ein Template aufgelöst wird sucht es bei der Speicherung nach den Antworten, welche auf seine Felder beziehen und ändert die Relation, so das die Antworten auf die neun kopierten Felder zeigen.
##### 02.02.01.00.02 Überlabente generelle Felder in den Formularen
- Situation:
    - Es existiert ein Formular  mit einem (oder mehreren) generellen Feld `X`,
    - Dieses Formular hat zu dem ein Template `Template-X` importiert.
    - Das `Template-X` wird angepasst und das generelles Feld `X` hinzugefügt.
- Was ist das Problem?
    - Pro Formular darf es von jedem generellem Feld nur eines im Formular vorhanden sein. Nun wären zwei mal das gleiche generelle Felder im Formular
- Was passiert jetzt?
    - Der User, der das generelle Feld zum Template hinzugefügt hat bekommt eine Nachricht.
        - In der Nachricht steht, das es ein Formular gibt welches dieses Template importiert hat und auch das hinzugefügte generelle Feld.
        - In der Nachricht steht auch um welche Formulare und generellen Felder es sich handelt
        - Ihm wird erklärt, dass wenn er jetzt speichert im anderen Formular, das generelle Feld gelöscht wird und die Antworten auf das Template umgeleitet werden </br>

##### 02.02.01.00.01 Überlappende generelle Felder in den Templates
- Situation:
    - Es existiert ein Formular  mit einem Template `Template-X` und einem Template `Template-Y`
    - Das Template `Template-Y` benutzt ein generelles Feld `Z`
    - Das `Template-X` wird angepasst und das generelles Feld `Z` hinzugefügt.
- Was ist das Problem?
    - Pro Formular darf es von jedem generellem Feld nur eines im Formular vorhanden sein. Nun wären zwei mal das gleiche generelle Felder im Formular
- Was passiert jetzt?
    - Der User, der das generelle Feld  zum Template hinzugefügt hat bekommt eine Nachricht.
        - In der Nachricht steht, das es eine Überlappung der generellen Felder mit einem anderen Template gibt
        - Es wird aufgelistet welche generellen Felder, Formulare und Templates dies betreffen
    - Der User kann das Template **nicht** Speichern </br>

#### 02.02.01.01 Templates wieder einfügen
Falls vor dem letzten Speichern, im Formular noch Felder des Aufgelösten Templates hinzugefügt werden, werden diese auf das neu eingefügte Template übertragen </br>

#### 02.02.01.02 Editor Repeater Validationen
Falls ein Template bearbeitet wird, werden die Validationen disabled. </br>

### 02.02.02 Templates deaktivieren

#### 02.02.02.00 Templates deaktivieren für alle Formulare
1. Gehe Sie in die Config `ffhs_custom_forms.php`
2. Entfernen Sie aus `editor_field_adder` den `TemplateAdder`
```php
/* 'editor_field_adder' => [  
    GeneralFieldAdder::class,  
    TemplateAdder::class,  
    CustomFieldAdder::class,  
],*/

'editor_field_adder' => [  
    GeneralFieldAdder::class,  
    CustomFieldAdder::class,  
],
```

- _Zurzeit kann man noch Templates zu den Formulararten erstellen, aber diese nicht mehr hinzufügen_ </br>

#### 02.02.02.01  Templates deaktivieren für eine Formularart
1. Gehe Sie in die Formularart entsprechende `DynamicFormConfiguration`
2. Überschreiben Sie die Methode `public static function editorFieldAdder()`
3. Fügen Sie folgendes in den Code ein
```php
public static function editorFieldAdder():array {  
    return [  
	    GeneralFieldAdder::class,  
	    CustomFieldAdder::class,  
	]
}
```

- _Zurzeit kann man noch Templates zu den Formulararten erstellen, aber diese nicht mehr hinzufügen </br>

#### 02.02.02.02 Mehr zu den Editor Field Adder
- Mehr zu den `Editor Field Adder` finden sie unter **11 Editor Anpassen** </br>

### 02.02.03 Weboberfläche // ToDo
#### 02.01.03.00 Ein Template erstellen
#### 02.01.03.01 Ein Template in einem Formular hinzufügen
#### 02.01.03.01 Error Meldungen

</br>

# 03 Formular Antworten
## 03.00 Formularantwort
### 03.01.00 Das Wichtigste
- Ein Formularantwort ist kann zu einem Formular zugeordnet werden.
- Die Formularantwort dient als bündelung der Antworten
- Wird im Code als `CustomFormAnswer` bezeichnet </br>

### 03.00.01 Eigenschaften
- Ein Formularfeld hat folgende wichtige Eigenschaften
    - `custom_form_id` => Verknüpfung zu dem `CustomForm`
    - `$short_title` => Zur wiedererkennung der Antworten im UI </br>

## 03.01 Field Antworten

### 03.01.00 Das Wichtigste
- Wird im Code als `CustomFieldAnswer` bezeichnet
- Speichert die Antworten der einzelnen Felder, </br>

### 03.01.01 Eigenschaften
-  Wichtige Eigenschaften:
    - `answer` => Die gespeicherte Antwort
    - `custom_form_answer_id` => Verknüpfung zu dem `CustomForm
    - `custom_field_id` => Verknüpfung zu dem Feld welches beantwortet wird.
        - Achtung: Das `CustomForm` Element des Verknüpften `CustomField` muss nicht zwingend das gleiche Formular sein, wie jenes welches mit der `CustomFormAnswer` verknüpft ist.
            - Der Grund dafür sind die `Templates` </br>

# 04 Feldtypen
## 04.00 Was sind Feldtypen
## 04.01 Neuer Feldtypen erstellen
### 04.01.00 Klasse erstellen
Um eine neue Feldtyp Klasse zu erstellen, muss die neue Klasse von `CustomFieldType` erben

```php
class LocationSelectorType extends CustomFieldType  
{  

    public static function getFieldIdentifier(): string {  
        return "loc_selector";
    }  

    public function icon(): String {  
	return  "carbon-select-window";  
     }
}
```
- `getFieldIdentifier()`
    - Mit Identifier wird die Formularart den `CustomField` und `GeneralField`. Es ist empfohlen diesen im nachhinein **nicht** zu ändern !!!!
- `icon()`
    - Das Icon welches angezeigt wird, wenn das Formular zusammengestellt wird.  </br>

### 04.01.01 Neuer TypeView erstellen
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
    - Diese Methode soll die Komponente beim bearbeiten der Formularantwort zurückgeben (`Form`)
    - `CustomField $record`: Das Model
    - `array $parameter`
        - `'viewMode'`
        - Bei den `CustomLayoutTypes`, sehe mehr bei __06 Layout Felder__
            - `'customFieldData'`
            - `'rendered'`
- `getViewComponent()`
    - Diese Methode soll die Komponente beim betrachten der Formularantwort zurückgeben (`Infolist`)
    - `CustomFieldType $type`: The type for them this view ist.
        - It is for the primary functions like:
            - `getToolTips(CustomField $record)`
            - `getLabelName(CustomField $record)`
    - `CustomFieldAnswer $record`: Das Model
    - `array $parameter
        - `'viewMode'`
        - Bei den `CustomLayoutTypes`, sehe mehr bei __06 Layout Felder__
            - `'customFieldData'`
            - `'rendered'` 
 </br>

### 04.01.02 TypeView registrieren
- Kehren sie zurück in ihre erstellten Typen Klasse zurück
- Fügen Sie die Methode `viewModes(): array` hinzu
```php
class LocationSelectorType extends CustomFieldType  
{  
    ...
     public function viewModes(): array {  
        return [
		'default'=>LocationSelectorTypeView::class,
		...
        ];
    }
}
```
- `viewModes(): array`
    - Die `viewModes` sind verschiedene Ansichten, diese können hier, in der globalen Konfigurationsdatei oder in den `DynamicFormConfiguration` ergänzt oder überschrieben werden
        - Sehe mehr bei __10 View-Modes__
    - `default` **muss** vorhanden sein. 
</br>

### 04.01.03 Typen registrieren
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
- `selectable_general_field_types`
    - Die hier eingetragenen Typen, können als Typen für die generellen Felder gebraucht werden. </br>

### 04.01.04 Translation
Der Feldtypen benötigt noch einen übersetzen Namen</br>

#### 04.01.04.00 Variante 1 Über das standard Language File
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

#### 04.01.04.01 Variante 2  Überschreiben des Standards
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

## 04.02 FormMapper
Der `FormMapper` vereinfacht das Rauslesen der Daten aus dem `CustomField`
- `getToolTips(CustomField|CustomFieldAnswer $record)`
- `getLabelName(CustomField|CustomFieldAnswer  $record)`
- `getIdentifyKey(CustomField|CustomFieldAnswer  $record)`
- Folgendes wird unter __05 Typenoptionen genauer erläutert__
    - `getOptionParameter(CustomField|CustomFieldAnswer $record, string $option)`
        - Gibt den Wert der TypeOption zurück (falls nicht vorhanden wird der default Wert zurückgegeben)
- Folgendes wird unter __07 Custom-Options Felder__
    - `getAllCustomOptions(CustomField $record):Collection`
        - Gibt alle möglichen `CustomOptions` Optionen zurück
    - `getAvailableCustomOptions(CustomField|CustomFieldAnswer $record)`
        - Gibt die für dieses Feld Ausgewählten Optionen Zurück</br>

## 04.03 Zyklus und Funktionen
### 04.03.00 Formular Editor
#### mutateOnTemplateDissolve(array $data):array
// ToDo
#### afterEditFieldSave(CustomField $field, array $rawData):void
// ToDo
#### afterEditFieldDelete(CustomField $field):void
// ToDo </br>

### 04.03.01 Formular Laden
#### prepareLoadFieldData(array $data): mixed
// ToDo </br>

### 04.03.02 Formular Speichern
#### updateFormComponentOnSave(Component $component, CustomField $customField, Form $form): void
Die Methode wird aussgeführt, bevor überhaupt die Daten für die Speicherung der Felder erhoben werden.  Dies  meint, dass man hier noch  die Compüonente an sich bearbeiten kann. um beispielsweise die Dateien von einem FileUpload mit `saveUploadedFiles` zu speichern.
**Diese Funktion ist vorallem wichtig beim Autosave**
#### prepareSaveFieldData(mixed $data): ?array
// ToDo
#### afterAnswerFieldSave(CustomFieldAnswer $field, mixed $rawData, array $formData):void
// ToDo </br>

### 04.03.03 Anderes
- `overwrittenRules():?array`
    - Überschreiben von den RuleType (Schaue __08 Regeln__)
- `overwrittenAnchorRules():?array`
    - Überschreiben von den AnchorRuleType (Schaue __08 Regeln__) </br>

## 04.04 `DynamicFormConfiguration` Einstellungen
### 04.03.00 Begrenzung der zur Auswahl gestellten Feldtypen für ein Formular
Es ist möglich die Feldtypen für eine bestimmte Formularart anzupassen. Standartmässig werden alle registrieren `CustomFieldType`
1. Gehe in die entsprechende `DynamicFormConfiguration`
2. Füge die Methode `formFieldTypes(): array` hinzu
3. Gebe die ausgewählten `CustomFieldType` an
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public  static function formFieldTypes():array{  
	    return [  
	      TextType::class,  
	      DateTimeType::class,  
	      EmailType::class,
	      ...  
	    ]; 
	}
}
```
</br>

## 04.05 Existierende Typen
//ToDo </br>

# 05 Typenoptionen in Formular Feldern (TypeOption)

## 05.00 Was ist mit Typenoptionen gemeint
- Mit Typenoptionen sind Optionen gemeint welche man bei einem Fled hinterlegen kann und diese in eine Weise beeinflusst. Diese Optionen können im Formeditor angepasst werden aber nicht wärend der Ausfüllung eines Formulars </br>

## 05.01 Hardcoded Optionen

### 05.01.00 Deaktivierbar
```php
class LocationSelectorType extends CustomFieldType  
{  
	...
	public function canBeDeactivate():bool {  
	    return true;  
	}  
	...
}
```
- `canBeDeactivate()`
    - Falls `false` wird der Slider `Aktive` in den Feldoptionen nicht angezeigt (Immer Aktive) </br>

### 05.01.01 Erforderlich
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
- `canBeRequired()``
    -  Falls `false` wird der Slider `Benötigt` in den Feldoptionen nicht angezeigt (Immer Aus) </br>


## 05.02 TypeOption Erstellen
### 05.02.00 Für was sind TypeOption?
In manchen Fällen benötigt wir mehr Optionen. Diese können mit `TypeOption`'s hinzugefügt werden. </br>

### 05.02.01 Erstellen einer TypeOption Klasse
- Die `TypenOption` Klasse dient für die Wiederverwendung der Optionen.
- Es können auch Optionen ohne diese `TypenOption` eingebunden werden (Sehe weiter Unten)
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
    - Die Komponente der `TypeOption` welche Angezeigt wird.
- Andere überschreibbare Methoden:
    - `mutateOnCreate(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim erstellen des `CustomField`'s
    - `mutateOnSave(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim speichern des `CustomField`'s
    - `mutateOnLoad(mixed $value, CustomField $field):mixed`
        - Verändert den Wert der Option beim laden des `CustomField`'s </br>

### 05.02.02 TypeOption im Feldtypen eintragen.

#### 05.02.02.00 TypeOption Klasse im Feldtypen eintragen
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function getExtraTypeOptions(): array {
		return [
			"my_option_name" => new MyOption(), // <= Standart
		];
	}
}
```
- `getExtraTypeOptions()`
    - Diese Optionen können beim Bearbeiten des `CustomField`s im Editor angepasst werden </br>

#### 05.02.02.01 FastTypeOption im Feldtypen eintragen
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function getExtraTypeOptions(): array {
		$myComponent = Toggle::make("several")  
			->label("Mehre auswählbar")
			->columnSpanFull()  
			->live();

		return [
			"my_fast_option" => new FastTypeOption(false, $myComponent),
		];
	}
}
```
- `FastTypeOption(mixed $defaultValue, Component $component)`
    - Um schnell eine `TypeOption` hinzuzufügen </br>

#### 05.02.02.03 Komponente einer vorhandenen TypeOption bearbeiten
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function getExtraTypeOptions(): array {
		return [
			'in_line_label' => (new InLineLabelOption()),
				->modifyComponent(fn(Toggle $toggle) => $toggle->columnStart(1)),
		];
	}
}
```
- `modifyComponent(fn(Component $component) => ...)`
    - Diese Funktion bietet die Möglichkeit einer vordefinierte `TypeOption` die Komponenten anzupassen </br>

## 05.03 TypeOption Benutzen
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
- Die `TypeOption` können am einfachsten mit Hilfe des  `FormMapper` bennuzt werden
    - `getOptionParameter(CustomField|CustomFieldAnswer $record, string $option)`
- Die roh Daten können über `$record->options` erreicht werden </br>

## 05.04 TypeOption für generelle Felder
- Das ganze wird ähnlich gehandhabt wie im __05.02.02  TypeOption im Feldtypen eintragen.___
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
    - Diese Optionen werden nur in den Generellen Felder angezeigt </br>

## 05.04.05 Standard TypeOption's
- Es existiert das Trait `HasBasicSettings`
- `HasBasicSettings` Bring folgende Optionen mit:
    - `column_span` => Spaltenweite
    - `in_line_label` => In Titel der Zeilen
    - `new_line_option` => Feld auf neuer Zeile
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
- Um weitere Optionen hinzufügen können Sie folgenden Methoden verwenden:
    - `extraOptionsBeforeBasic()` => Vor der Basic-Setting
    - `extraOptionsAfterBasic()` => Nach den Basic-Settings </br>

# 06 Layout-Felder
## 06.00 Layout-Felder
- Layout Felder sind Felder die eine Verschachtelung aufweisen.
    - Beispiel `Sections`
- Layout Felder werden beim Rendern des Formulars anders gehandhabt. </br>

## 06.01 FeldLayoutType
### 06.01.00 FeldLayoutType
- `FeldLayoutType` ist die bereitgestellte Klasse für neue Layout-Typen
- Er bietet folgende Funktionen
    - Felder können, in das Feld verschachtelt werden mithilfe von Pfeilen
    - Die Felder die Untergeordnet sind werden einzeln gerendert und als Parameter mitgeliefert  </br>

### 06.01.01 FeldLayoutType Erstellen
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
    -  Der `CustomLayoutType` wird beim Rendern des Formulars anders behandelt, er erhält bei der View als Parameter die Formularfelder, welche ihm untergeordnet sind. </br>

### 06.01.02 FieldTypeView Besonderheiten
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
    - In dem Parameter `"rendered"` sind die weiteren gerenderten Formfelder bzw. die Infolistfelder drinnen.
- `$parameter["customFieldData"]`
    - In dem Parameter `"customFieldData"` sind die Rohdaten der `CustomField`'s enthalten. </br>


## 06.02 Nested Layout-Felder
### 06.02.00 Erklärung
- Die nested Layout-Felder sind Layout Felder, welche nicht alleine Stehen können. Das bedeutet, dass diese Felder noch bestimmte Unterfelder benötigen, beispielsweise Tabs oder Wizards
- Diese Felder bestehen aus zwei Teilen
    - `CustomNestLayoutType` => Das Feld welches zu oberste von der Hierarchie sein soll. (Beispiel: `Tabs`)
        - Die `rendered` Felder, welche Sie zurückbekommen, sind nur Felder von dem Typen Ihres hinterlegten `CustomEggLayoutType`'s
    - `CustomEggLayoutType` =>Das Feld welches eine Schicht tiefer sein soll. (Beispiel: `Tabs\Tab`)
        - Die `CustomEggLayoutType` werden nicht unter `selectable_field_types` in der Config eingetragen werden, da diese peer Repeater-Actions hinzugefügt werden können
- Diese Felder bieten direkt folgende Funktionen:
    - Es Können über die `(+)` Action neue Eggs zu dem Nest hinzugefügt werden.
    - Es gibt eine neue `PullInAction` die Alle Felder haben welche unter dem Nest stehen.
        - Wenn ein Feld hochgeschoben werden soll, kann der User mithilfe von einem Select auswählen wohin das Feld verschoben werden soll.
    - Es gibt eine neue `PullOuAction` die Alle Felder haben welche in einem Ei sind.
        - Diese Action verschiebt das Feld ausserhalb des Eies </br>

### 06.02.01 Neuer NestedType erstellen (`CustomNestLayoutType` & `CustomEggLayoutType`)
#### 06.02.01.00 Erstellen des  `EggLayoutType`
- Erstellen Sie eine neue Klasse für den Eitypen und erben Sie von `EggLayoutType`
    - Sie können diese Klasse wie einen normalen `CustomLayoutTypen` behandeln
```php
class TabEggType extends CustomEggLayoutType  
{  
    public static function getFieldIdentifier(): string {  
        return "tab";  
    }  
  
    public function viewModes(): array {  
        return [  
            "default"=> TabEggTypeView::class  
        ];  
    }  
  
    public function icon(): string {  
        return "tabler-slideshow";  
    }  
  
}
```
</br>

- Erstellen Sie eine neue Klasse für die View des Eitypen und erben Sie von `FieldTypeView`
    - Sie können diese Klasse wie einen normalen `FieldTypeView` für einen `CustomLayoutTypen` behandeln
```php
class TabEggTypeView implements FieldTypeView  
{  
    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component { 
        return Tabs\Tab::make(FormMapper::getLabelName($record))   
            ->schema($parameter["rendered"]);  
    }  
  
    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {  
        return Tabs\Tab::make(FormMapper::getLabelName($record))  
            ->schema($parameter["rendered"])  
            ->columnSpanFull()
            ->columnStart(1)  ;  
    }  
}
```
</br>

#### 06.02.01.00 Erstellen des `CustomNestLayoutType`
- Erstellen Sie eine neue Klasse für den Nesttypen und erben Sie von `CustomLayoutType`
    - Sie können diese Klasse grundlegend wie einen normalen `CustomLayoutTypen` behandeln
```php
class TabsCustomNestType extends CustomNestLayoutType  
{  
    public static function getFieldIdentifier(): string {  
        return "tabs";  
    }  
  
    public function viewModes(): array {  
        return  [  
          'default'=> TabsNestTypeView::class,  
        ];  
    }  
    public function icon(): string {  
       return "carbon-new-tab";  
    }  


    public function getEggType(): CustomEggLayoutType {  
        return new CustomTabCustomEggType();  
    }  
}
```
- Die Funktion `function getEggType(): CustomEggLayoutType` soll den passenden Eitypen zurückgeben.
    - Die Funktion wird verwendet um neue Eier zum Nest hinzuzufügen.
      </br>

- Erstellen Sie eine neue Klasse für die View des Nesttypen und erben Sie von `FieldTypeView`
    - Sie können diese Klasse grundlegend wie einen normalen `FieldTypeView` für einen `CustomLayoutTypen` behandeln
```php
class TabsNestTypeView implements FieldTypeView  
{  
  
    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {  
        
        return Tabs::make($FormMapper::getLabelName($record))  
            ->columnSpan(FormMapper::getOptionParameter($record,"column_span"))  
            ->inlineLabel(FormMapper::getOptionParameter($record,"in_line_label"))  
            ->columnStart(FormMapper::getOptionParameter($record,"new_line_option"))  
            ->tabs($parameter["rendered"]);  //<===================
    }  
  
    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {  
	    return \Filament\Infolists\Components\Tabs::make($FormMapper::getLabelName($record))  
			->columnStart(1)  
			->tabs($parameter["rendered"])  
		      ->columnSpanFull(); //<===================
	}
}
```
</br>

# 07 Custom-Options Felder

## 07_00 Feldtypen mit Auswahloptionen (`CustomOption)
- Feldtypen mit Auswahloptionen sind Typen welche Vordefinierte Antworten haben, wie Beispielsweise ein Select-Feld
- Besonderheiten
    - Falls das Auswahlfeld ein Generelles Feld ist, können für die Optionen `identifier` hinterlegt werden, für den export.
    - Auswahlfelder können bei Regeln Anker können diese direkt auf die Optionen zugreifen und müssen nicht Manuel eingegeben werden
        - Sehe mehr bei __08 Regeln__ </br>

## 07_01 Unterschied zwischen `CustomOption` und `TypeOption`
- `TypeOption` sind Optionen für die Felder, welche Auswirkungen auf die Einstellungen haben können
- `CustomOption` sind Auswahloptionen für Felder. </br>

## 07_02 Technische Besonderheiten
- Die `CustomOption` benutzen die `TypeOption` und werden nicht viel anders behandelt als andere `FieldTypen`

## 07_03 Feldtypen mit Auswahloptionen erstellen

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
    - Fügt eine standard `getInfolistComponent` Methode hinzu. Diese kann Unterscheiden ob eine oder mehrere Optionen Auswählbar sind, und stellt dies dementsprechend dar. </br>


## 07.04 Weboberfläche // ToDo
### 07.04.00 CustomOption's bei Felder
### 07.04.01 CustomOption's bei generellen Felder


# 08 Regeln
## 08.00 Was sind Regeln?
### 08.00 Kurzgefasst
- Eine Regel im Gesamt Paket wird als `FieldRule` im Code bezeichnet
- Eine Regel kann an einem Feld hinzugefügt werden
- Diese Regel kann beispielsweise bei einer bestimmten Bedingung ein Feld Verstecken
- Eine Regel besteht aus zwei Teilen einen Anker und einer Regel </br>

### 08.01 Aufbau
#### 08.00.00 Anker
- Der Anker ist dafür da die Bedingungen zu überprüfen.
- Der Anker ist ein Teil in der `FieldRule` und beinhaltet:
    - Einen Identifier für den Ankertypen (Wie die `CustromFieldType`'s)
    - Einen Datencontainer für die nötigen Einstellungen zu speichern
- Stichwort ist der `FieldRuleAnchorType`
#### 08.00.01  Regel
- Die Regel ist dafür da die Regel auszuführen.
- Die Regel ist ein Teil in der `FieldRule` und beinhaltet:
    - Einen Identifier für den Typen (Wie die `CustromFieldType`'s)
    - Einen Datencontainer für die nötigen Einstellungen zu speichern
- Stichwort ist der `FieldRuleType` </br>

### 08.02 FieldRule
#### 08.02.00 Eigenschaften
- Ein Formularfeld hat folgende Eigenschaften
    - `custom_field_id` => Verknüpfung zum `CustomField` auf  das die Regel läuft
    - `anchor_identifier` => Der Ankertypen
    - `anchor_data` => Die Daten welcher der Anker benötigt
    - `rule_identifier` => Der Regeltypen
    - `rule_data` =>  Die Daten welcher die Regeln benötigt
    - `execution_order` => Die Reihenfolge mit welcher die Regeln ausgeführt werden. </br>


## 08.01 Feld Regel hinzufügen über das UI
//ToDo </br>

## 08.02 Existierende Typen
### 08.02.00 Ankertypen
#### 08.02.00.00 `ValueEqualsRuleAnchor`
- `identifier` => `value_equals_anchor`
- Dieser Anker kann folgendes:
    - Einen bestimmten Wert eines anderen Feldes abrufen und schauen ob dieser den gleichen Input hat (Text)
    - Einen bestimmten Wert eines anderen Feldes abrufen und schauen ob dieser den gleichen Input hat (Boolean)
    - Einen bestimmten Wert eines anderen Feldes abrufen und schauen ob dieser in einem Nummernbereich ist (Nummer)
    - Einen bestimmten Wert eines anderen Feldes abrufen und schauen ob dieser eine oder mehre Option ist (`CustomOptions`) </br>

### 08.02.00 Regeltypen
#### 08.02.00.00 `RequiredRuleType`
- `identifier` => `is_required_rule
- Diese Regel kann folgendes:
    - Stellt die Komponente auf Benötigt um
    - Hat einen Invertiert Modus
        - Nicht Benötigt wenn Anker aktive </br>
#### 08.02.00.01 `HiddenRuleType``
- `identifier` => `is_hidden_rule
- Diese Regel kann folgendes:
    - Versteckt die Komponente
    - Hat einen Invertiert Modus
        - Nicht Versteckt wenn Anker aktive </br>
#### 08.02.00.02 `DisabledRuleType``
- `identifier` => `is_disabled_rule
- Diese Regel kann folgendes:
    - Disabled die Komponente
        - Nicht disabled wenn Anker aktive </br>
#### 08.02.00.03 `ChangeOptionRuleType``
- `identifier` => `change_options_rule
- Diese Regel kann folgendes:
    - Ändert die auswählbaren `CustomOptions` </br>

## 08.03 Typen hinzufügen
### 08.03.00 Ankertypen hinzufügen

#### 08.03.00.00 Schritt 1 Klasse deklarieren
```php
class MyRuleAnchorType extends FieldRuleAnchorType  
{  
    public static function identifier(): string {  
        return "value_equals_anchor";  
    }  
  
    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {  
        return TextInput:make("my_importent_value");
    }  
  
  
    public function shouldRuleExecute(array $formState, Component $component, FieldRule $rule): bool {  
       return true; //to your decision
    }
    
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
    - `mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):array`
        - //ToDo
    - `getDisplayName(array $ruleData, Repeater $component, Get $get): string`
        - **Der Anzeigename in der Regelsektion**
    - `afterAllFormComponentsRendered(FieldRule $rule, Collection $components):void`
        - Zugriff auf alle Komponenten von dem Formular (Bzw. von dem gespaltenen Formular). Wir bei dem  ValueEqualsRuleAnchor verwendet um die Felder live zu stellen</br>

#### 08.03.00.01 Schritt 2 Registrieren
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge den `FieldRuleAnchorType` bei `field_rule_anchor_types` hinzu
```php
return [  
	...
	"field_rule_anchor_types"=>[  
	    MyRuleAnchorType::class  
	],
];
```
</br>

### 08.03.01 Regeltypen hinzufügen

#### 08.03.01.00 Schritt 1 Klasse deklarieren
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
		$anchorDecisions = $this->>canRuleExecute($component,$rule); // Bekomme die Entscheidung des Ankers 
		if(!anchorDecisions) return;
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
    - `beforeComponentRender(CustomField $customField, FieldRule $rule)`
    - `afterComponentRender(Component|InfoComponent $component, CustomField $customField, FieldRule $rule): Component|InfoComponent`
    - `mutateLoadAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed`
    - `mutateSaveAnswerData(mixed $answerData, FieldRule $rule, CustomFieldAnswer $answer):mixed`
    - `afterAnswerSave( FieldRule $rule, CustomFieldAnswer $answer):void`
    - `mutateRenderParameter(array $parameter, CustomField $customField, FieldRule $rule): array`
        - Verändere die Parameter welche den Felder beim Rendern mitgegeben werden
    - `mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):arra`
        - //ToDo
          </br>

#### 08.03.01.01 Schritt 2
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `field_rule_types` hinzu
```php
return [  
	...
	"field_rule_types"=>[  
	    IsMyRuleType::class  
	],
];
```
</br>

## 08.03 `CustomFieldType` Einstellungen
### 08.03.00 Ankertypen Einstellen für ein Feld
```php 
class LocationSelectorType extends CustomFieldType  
{  
	public function overwrittenAnchorRules():?array {
	    return [
		    MyRuleAnchorType::class
	    ];  
	}
}
```
- In Ihrem `CustomFieldType` überschreibe die Methode `overwrittenAnchorRules():?array`
- Der Wert `null` bedeutet, dass die Regeln nicht überschreiben werden
- Dies betrifft nur diesen Feldtypen</br>
-
### 08.03.01 Regeltypen Einstellen für ein Feld
```php 
class LocationSelectorType extends CustomFieldType  
{  
	public function overwrittenRules():?array {
	    return [
		    IsMyRuleType::class
	    ];  
	}
}
```
- In Ihrem `CustomFieldType` überschreibe die Methode `overwrittenRules():?array`
- Der Wert `null` bedeutet, dass die Regeln nicht überschreiben werden
- Dies betrifft nur diesen Feldtypen </br>

## 08.04 `DynamicFormConfiguration` Einstellungen
### 08.04.00 Ankertypen Einstellen für ein Formular
```php
class CourseApplications extends DynamicFormConfiguration {
	public static function anchorRuleTypes(): array {  
	    return [
		    MyRuleAnchorType::class
	    ];
	}
}
```
- In Ihrem `DynamicFormConfiguration` überschreibe die Methode `anchorRuleTypes():array`
- Dies betrifft nur diese Formularart </br>

### 08.04.01 Regeltypen Einstellen für ein Formular
```php
class CourseApplications extends DynamicFormConfiguration {
	public static function ruleTypes(): array {  
	    return [
		    IsMyRuleType::class
	    ];
	}
}
```
- In Ihrem `DynamicFormConfiguration` überschreibe die Methode `ruleTypes():array`
- Dies betrifft nur diese Formularart </br>

## 08.05 Configurations Einstellungen

### 08.05.00 FieldRuleType Option
```php
return [  
	"field_rule_anchor_types"=>[ 
		ValueEqualsRuleAnchor::class,  
	],
];
```
- Unter der Config `ffhs_custom_forms` und dem Punkt `field_rule_anchor_types`
- Wenn hier einen Ankertypen entfernt wird, wird dieser nicht mehr zum hinzufügen angezeigt.
- Diese Einstellungen gelten für alle Formulare </br>

### 08.05.01 FieldRuleType Option
```php
return [  
	"field_rule_types"=>[ 
		RequiredRuleType::class,  
		HiddenRuleType::class,  
		DisabledRuleType::class,  
		//ChangeOptionRuleType::class, 
	     IsMyRuleType::class  
	],
];
```
- Unter der Config `ffhs_custom_forms` und dem Punkt `field_rule_types`
- Wenn hier einen Regeltypen entfernt wird, wird dieser nicht mehr zum hinzufügen angezeigt.
- Diese Einstellungen gelten für alle Formulare </br>

## 08.06 Anker und Regeln Lebenszyklus
//ToDo </br>


# 09 Formulare einbinden

## 09.00 Editor

### 09.00.00 Einbinden
- Der Editor kann mit der Komponente `EmbeddedCustomFormEditor` eingebunden werden.
- Wichtig ist das dieser leider **NICHT** in einem Modal geöffnet werden kann, ohne eine menge Fehlermeldungen
```php
function form($form){
	return $form
		->schema([
			EmbeddedCustomFormEditor::make("customForm")
		]);
}
```
- `function make(Closure|string $relationship): static`
    - Beim `$relationship` Parameter soll der Namen der Relationship-Name zu dem zu bearbeitenden `CustomForm` hinterlegt werden </br>

## 09.02 Formular

### 09.01.00 Einbinden
- Das Formular kann mit der Komponente `EmbeddedCustomForm` eingebunden werden.
```php
function form($form){
	return $form
		->schema([
			EmbeddedCustomForm::make("customForm","default")
		]);
}
```
- `function make(Closure|string $relationship, string|Closure $viewMode= "default"): static`
    - Beim `$relationship` soll der Namen der Relationship-Name zu dem zu bearbeitenden `CustomFormAnswer` hinterlegt werden
    - Beim `$viewMode` soll der `ViewMode` eingetragen werden (Sehe mehr bei __10 View-Modes__) </br>

### 09.01.01 Funktionen
#### 09.01.01.00 Automatisches Speichern
- Es gibt die Möglichkeit das Formular automatisch abzuspeichern nach jede Änderung
```php 
	EmbeddedCustomForm::make("customForm","default")
		->autoSave()
```
- `function autoSave(bool|Closure $isAutoSave = true):static`
    - Der Parameter `$isAutoSave` gibt zurück ob das Formular automatisch gespeichert werden soll oder nicht. </br>

#### 09.01.01.01 Automatische View Mode
- Es gibt die Möglichkeit den `ViewMode` automatisch zu bestimmen
    - Hier geht es vor allem um die `ViewModes`  welche in der `DynamicFormConfiguration`  unter `displayEditMode()` und `displayCreateMode()` abgelegt sind.
    - Die Methode überprüft ob in den Antworten, eine Antwort vorhanden ist oder nicht und wählt dann den dementsprechenden `ViewMode` aus
```php 
	EmbeddedCustomForm::make("customForm","default")
		->autoViewMode()
```
- `function autoViewMode(bool|Closure $autoViewMode = true):static`
    - Der Parameter `$autoViewMode` gibt zurück ob das Formular automatisch gespeichert werden soll oder nicht. </br>

#### 09.01.01.02 Teilbearbeitungen (Teilanzeige des Formulars)

##### 09.01.01.02.00 Layoutspaltung
- Mit der lLayoutspaltung ist es Möglich nur den Inhalt in der ersten Layout-Komponente mit dem Entsprechenden Typen darzustellen
- Wozu ist das gut?
    - Beispielsweise, man möchte das Formular in drei Teile aufsplitten.
    - Als erstes erstellt man für jeden Teil einen eigenen Feldtypen und fügt diese anschliessend als generelle Felder hinzu.
    - Der User kann dann den Teil in sein Formular reinnehmen und die Felder welche in diese Section sollen in dieses generellen Feld reinpacken.
    - Nun kann man das Formular mithilfe von dieser Methode Splitten und die Einzelteile an verschiedenen Orten dar packen
```php 
	EmbeddedCustomForm::make("customForm","default")
		->useLayoutTypeSplit()
		->layoutTypeSplit(Section::type)
```
- `function useLayoutTypeSplit(bool|Closure $useLayoutTypeSplit = true):static`
    - Mit dem `$useLayoutTypeSplit` Parameter kann man bestimmen ob das Formular soll mithilfe der Layoutspaltung gespalten werden soll.
- `function layoutTypeSplit(CustomLayoutType|Closure|null $layoutTypeSplit):static`
    - Beim `$layoutTypeSplit` wird der Feldtypen übergeben an welchem gespalten werden soll. </br>
    -
##### 09.01.01.02.01 Positionsspaltung
- Mit der Positionsspaltung ist es Möglich nur einen bestimmten Teil des Formulars darzustellen
```php 
	EmbeddedCustomForm::make("customForm","default")
		->usePoseSplit()
		->poseSplit([1,10])
```
- `function usePoseSplit(bool|Closure $usePosSplit = true):static`
    - Mit dem `$usePosSplit` Parameter kann man bestimmen ob das Formular soll mithilfe der Positionsspaltung gespalten werden soll.
- `function poseSplit(array|Closure|null $posSplit):static`
    - Beim `$posSplit` werden die Positionen der Spaltung übergeben mithilfe eines Array's und diesem Schema: `[$beginPos,$endPos]` </br>

##### 09.01.01.02.02 Feldspaltung
- Mit der Feldspaltung ist es Möglich den Contend eines Spezifischen Feldes darzustellen (Am besten von einem layout Feld)
```php 
	EmbeddedCustomForm::make("customForm","default")
		->useFieldSplit()
		->fieldSplit(fn($record)=> $record->specialField()->get())
```
- `function useFieldSplit(bool|Closure $useFieldSplit = true):static`
    - Mit dem `$fieldSplit` Parameter kann man bestimmen ob das Formular soll mithilfe der Feldspaltung gespalten werden soll.
- `function fieldSplit(CustomField|Closure|null $fieldSplit):static`
    - Beim `$fieldSplit` wird das `CustomField mitgegeben` </br>

## 09.02 Infolist

### 09.02.00 Einbinden
- Die Infolist kann mit der Komponente `EmbeddedAnswerInfolist` eingebunden werden.
```php
function infolist($infolist){
	return $infolist
		->schema([
			EmbeddedAnswerInfolist::make("customForm","default")
		]);
}
```
- `function make(CustomFormAnswer|Closure $model, string|Closure $viewMode = "default"): static`
    - Beim `$relationship` soll der Namen der Relationship-Name zu dem zu bearbeitenden `CustomFormAnswer` hinterlegt werden
    - Beim `$viewMode` soll der `ViewMode` eingetragen werden (Sehe mehr bei __10 View-Modes__) </br>

### 09.02.01 Funktionen

#### 09.02.01.00 Automatische View Mode
- Es gibt die Möglichkeit den `ViewMode` automatisch zu bestimmen
    - Hier geht es vor allem um die `ViewModes`  welche in der `DynamicFormConfiguration`  unter `displayEditMode()` und `displayCreateMode()` abgelegt sind.
    - Die Methode überprüft ob in den Antworten, eine Antwort vorhanden ist oder nicht und wählt dann den dementsprechenden `ViewMode` aus
```php 
	EmbeddedCustomForm::make("customForm","default")
		->autoViewMode()
```
- `function autoViewMode(bool|Closure $autoViewMode = true):static`
    - Der Parameter `$autoViewMode` gibt zurück ob das Formular automatisch gespeichert werden soll oder nicht. </br>

#### 09.02.01.01 Teilbearbeitungen (Teilanzeige des Formulars)
Gleich wie bei __09.01.01.02 Teilbearbeitungen (Teilanzeige des Formulars)__ </br>

# 10 View-Modes

## 10.00 Was sind `ViewModes`
- Ein Formular besteht aus Feldern. Diese Felder haben Eigenschaften, aber jetzt kann es sein, dass man beim Erstausfüllen der Formulars eine andere Ansicht braucht als beim bearbeiten der Antworte. Oder der Kunde seht eine andere Übersicht als der Administrator oder eine bestimmte Abteilung. Und hier kommen die `ViewModes` hinzu. Die `ViewMode`'s können sozusagen das "Designe" der Formulare überschreiben oder für verschiedene Zwecke auslegen
- Kurzgesagt, sie exisiteren für verschiednen Ansichten
- Die `ViewMode` besteht aus einem `Key` und einem `FieldTypeView` </br>

## 10.01 Überschreibungslevels der `ViewModes`
- Im `CustomFieldType` selbst (Level 0)
- In der Plugin-Konfigurationsdatei (Level 1)
- Und in der `DynamicFormConfiguration` (Level 2)
  Wenn die View in der Plugin-Konfigurationsdatei überschreiben wird, wird diese verwendet. Wenn sie zusätzlich oder in der `DynamicFormConfiguration` überschreiben wird gilt die `ViewMode` von der `DynamicFormConfiguration`. </br>

## 10.02 Erstellen einer neuen `TypeView`
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

## 10.03 Änderungen in den `CustomFieldType`'s
### 10.03.01 Hinzufügen einer `ViewMode` über `CustomFieldType`
Sehe __04 Feldtypen (04.01.02 TypeView registrieren)__
</br>

## 10.04 `DynamicFormConfiguration` Einstellungen
### 10.04.00 Überschreiben/Hinzufügen einer `ViewMode` über die `DynamicFormConfiguration`
1. Gehe in die entsprechende `DynamicFormConfiguration`
2. Füge die Methode `getOverwriteViewModes(): array` hinzu
3. Gebe die ausgewählten `CustomFieldType` an
4. Füge im Array die `CustomFieldType` hinzu bei welcher eine `FieldView` hinzugefügt werden soll.
3. Ergänze den `Key` (Namen) der `ViewMode` und die `FieldView` Klasse hinzu
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function getOverwriteViewModes(): array {  
	    return [  
	      TextType::class =>>[
		      "default"=>MyNewDefaultView::class, //Überschreiben
		      "super_view"=>MyNewSuperView::class, //Hinzufügen
	      ],
	    ];  
	}
}
```
</br>

### 10.04.01  Der `DynamicFormConfiguration` Default `ViewMode`
Es ist möglich die default `ViewMode` für verschiedene anwendungsfälle einstellen
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function displayViewMode(): array {  
	    return "super_view";
	}
}
```
- `function displayMode():string` Standardeinstellung, wenn die anderen Methoden nicht überschreiben sind.
- `function displayViewMode():string` Bei der Ansicht der Infolist
- `function displayCreateMode():string` Die Ansicht wenn eine neue Antwort erstellt wird.
- `function displayEditMode():string` Die Ansicht beim bearbeiten des Formulars
  </br>

## 10.05 Configurations Einstellungen
### 10.05.00 Überschreiben und Ergänzen
1. Gehe in die config `ffhs_custom_forms.php`
2. Füge die `FieldTypeView` bei `view_modes` hinzu
```php
return [
	'view_modes' => [  
		"default"=>MyNewDefaultView::class, //Überschreiben
		"super_view"=>MyNewSuperView::class, //Hinzufügen
	],
]
```
</br>

# 11 Form Editor Anpassen
## 11.00 Repeater Item Actions

### 11.00.00 Erklärung
- Es ist möglich die Item Actions der `CustomField`s im Editor anzupassen
- Dies ist nützlich wenn Sie Beispielsweise eine neue Action hinzufügen möchten die Spezifisch für Ihr Feld soll funktionieren. </br>
### 11.00.01 Erstellung einer Action
- Erstelle eine neue Klasse welche von `RepeaterFieldAction` erbt
- Definieren Sie Ihre Action
- Fügen Sie folgende Zeile hinzu `$this->isVisibleClosure($record,$typeClosers)`
    - Damit die Action nur dort Angezeigt wird, wo dieses dies auch soll.
```php
class MyRepeaterAction extends RepeaterFieldAction  
{  
    public function getAction(CustomForm $record, array $typeClosers): Action {  
        return Action::make('edit')  
            ->action(fn() => dd("Hi"))  
	       ->visible($this->isVisibleClosure($record,$typeClosers)); //Wichtig
    }  
}
```
</br>

#### 11.00.01.00 Hintergrund
- Repeater Actions können nicht für jedes Item Individuell hinzugefügt werden, daher müssen sie an den Orten versteckt werden, wo diese nicht gebraucht werden. </br>

### 11.00.02 Hinzufügen einer Action
#### 11.00.02.00 Hinzufügen einer Action welche am Typen gebunden ist
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function repeaterFunctions(): array {  
		return array_merge($parent::repeaterFunctions(),[
			MyRepeaterAction::class => MyRepeaterAction::getDefaultTypeClosure($this)
		]);
	}
}
```
- `function getDefaultTypeClosure(CustomFieldType $type): Closure` Fügt die vordefinierte Closure hinzu
    -  `$type` der Typen auf der die Action gebunden werden soll </br>

#### 11.00.02.01 Hinzufügen einer Action welche an eine andere Bedingung gebunden ist
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function repeaterFunctions(): array {  
		return array_merge($parent::repeaterFunctions(),[
			MyRepeaterAction::class => function (CustomForm $record,Get $get, array $state, array $arguments) {
				$fieldData = $state[$arguments["item"]];
				return !empty($fieldData["super_property"])
			} 
		]);
	}
}
```
</br>

## 11.01 Repeater Validationen
### 11.01.00 Erklärung
- Der Repeater validiert im Editor die `CustomFields`
- **Wichtig** bei den Templates werden die Validationen nicht ausgeführt, der Code dafür ist in der `DynamicFormConfiguration` unter der Methode `editorValidations(CustomForm $form)` </br>

### 11.01.01 `FormEditorValidation` erstellen
- Erstelle eine neue Klasse welche von `FormEditorValidation` erbt
```php
class FormEditorMyValidation extends FormEditorValidation  
{  
    public function repeaterValidation(CustomForm $record, Closure $fail, array $value, string $attribute):void {  
		if(rand(0,1)) $fail($failureMessage);  
    }  
}
```
</br>

### 11.01.02 In der Konfiguration registrieren
1. Gehe in die Config `ffhs_custom_forms.php`
2. Füge die `DynamicFormConfiguration` bei `custom_form_editor_validations` hinzu
```php
return [
	'custom_form_editor_validations' => [  
	    FormEditorGeneralFieldValidation::class,
	    FormEditorMyValidation::class
	],
];
```
</br>

### 11.01.03 In der `DynamicFormConfiguration` Überschreiben
1. Gehe in Ihre `DynamicFormConfiguration` Klasse
2. Überschreiben Sie die Methode `function editorValidations(CustomForm $form):array`
```php
class CourseApplications extends DynamicFormConfiguration  
{
	public static function editorValidations(CustomForm $form):array {  
	    if($form->is_template) return [];  
	    return [
		    FormEditorGeneralFieldValidation::class,
		    FormEditorMyValidation::class
	    ];  
	}
}
```
</br>

## 11.02 Repeater Contend
### 11.02.00 Erklärung
- Standartmässig haben die `CustomField`'s im Editor keinen Contend in den Repeatern und man kann sie auch nicht aufklappen
- Mit folgenden Schreiten können Sie in den Repeater von ihrem Feldtypen etwas hinzufügen </br>

### 11.02.01 Repeater Contend zu Typen hinzufügen
- Gehen Sie zu ihrem `CustomFieldType`
- Überschreiben Sie die Methode (`editorRepeaterContent(CustomForm $form, array $fieldData): ?array`)
```php
class LocationSelectorType extends CustomFieldType  
{  
	public function editorRepeaterContent(CustomForm $form, array $fieldData): ?array {  
	    return [Placeholder::make('My-Content')];  
	}
}
```
- `function editorRepeaterContent(CustomForm $form, array $fieldData): ?array`
    - Wenn der Rückgabewert `null` ist, dann hat das Field in seinem Repeater-Item keine Content und man kann dieses nicht aufklappen.
    - `$form` Das Formular welches gerade bearbeitet wird
    - `$fieldData` Die Felddaten des Feldes, welches diesen Typen hat </br>

## 11.03 Field Item Name
- Das Item-Label kann mit `nameFormEditor` und `nameBeforeIconFormEditor` angepasst werden.
    - `$state` sind die Daten des `CustomFields`
- `nameFormEditor` Ist für den Namen und das nach dem Namen folgende angedacht
- `nameBeforeIconFormEditor` Ist für Badge's angedacht
```php 
class LocationSelectorType extends CustomFieldType   
{
	public function nameFormEditor(array $state):string {  
	    return $state["name_de"] . " Something very special";  
	}  
	  
	public function nameBeforeIconFormEditor(array $state):string {  
	    $newBadge = new HtmlBadge("Neuer Badge", Color::rgb("rgb(34, 135, 0)"));
	    return parent::nameBeforeIconFormEditor() . $newBadge;  
	}
}
```
</br>

## 11.04 Angezeigte Feldtypen in Config
1. Gehe in die Config `ffhs_custom_forms.php`
2. Bei `custom_form_editor_validations`
```php 
return [
	"selectable_field_types" => [  
	    CheckboxType::class,  
	    DateTimeType::class,  
	    DateType::class,  
	    EmailType::class,  
	    NumberType::class,  
	    TextAreaType::class,  
	    TextType::class,  
	    SectionType::class,  
	    IconSelectType::class,  
	    SelectType::class,  
	    RadioType::class,  
	    CheckboxListType::class,  
	    ToggleButtonsType::class,  
	],
];
```
- Diese Felder werden im Editor angezeigt zum hinzufügen.  </br>

## 11.05 Field Adder
### 11.05.00 Erklärung
- An der Seite befinden sich drei Abschnitte, `Generelle Felder hinzufügen`, `Template hinzufügen`, `Spezifisches Feld hinzufügen`. Diese sind sogenannte `CustomFieldAdder`
- `CustomFieldAdder` sind dafür angedacht, neue Möglichkeiten hinzuzufügen um `CustomField`'s hinzuzufügen.
- Wenn man beispielsweise keine Templates verwenden möchte, kann der Template-Adder in der Config entfernt werden (Sehe __02 Formular Felder, Generelle Felder und Templates__ unter __02.02.02.00 Templates deaktivieren für alle Formulare__) </br>

### 11.05.01 Standard Adder
#### 11.05.00 Generelle Felder Adder
- Zum hinzufügen von generellen Felder </br>

#### 11.05.01 Template Adder
- Zum hinzufügen von Templates </br>

#### 11.05.02 Field Adder
- Zum hinzufügen von spezifischen Feldern </br>

### 11.05.02 Adder überschreiben
#### 11.05.02.00 Adder über die `DynamicFormConfiguration` überschreiben
- Gehen Sie in Ihre `DynamicFormConfiguration`
- Überschreiben Sie die Methode `editorFieldAdder`
- Diese Änderungen sind nur auf diese Formularart begrenzt
```php
class CourseApplications extends DynamicFormConfiguration   
{
	public static function editorFieldAdder():array {  
	    return [  
		    GeneralFieldAdder::class,  
		    //TemplateAdder::class,  
		    CustomFieldAdder::class
	    ];
	}
}
```
 </br>

#### 11.05.02.01 Adder über die Configuration überschreiben
1. Gehe in die Config `ffhs_custom_forms.php`
2. Bei `editor_field_adder`
```php
return [
	'editor_field_adder' => [  
	    GeneralFieldAdder::class,  
	   // TemplateAdder::class,  
	    CustomFieldAdder::class  
	],
];
```
 </br>

### 11.05.02 Adder Erstellen
#### 11.05.02.00 Klasse erstellen
- Erstellen Sie eine neue Klasse die von `FormEditorFieldAdder` erbt
```php
class CustomFieldAdder extends FormEditorFieldAdder  
{  
  
    function getTitle(): string {  
        return  "Spezifische Felder"; 
    }  
  
  
    function getSchema(): array {  
        return [  
            Actions::make([  
		    Action::make("add_my_field_action")  
		        ->action(function ($set, Get $get, array $data) {  
			        $data = [  
					    "type" => LocationSelectorType::getFieldIdentifier(),  
					    "options" => (new LocationSelectorType()->getDefaultTypeOptionValues(),  
					    "is_active" => true,  
					    "identify_key" => uniqid(),  
					];
					
			          $this->addCustomFieldInRepeater($data, $get, $set);  
		        })  
		]);
        ];  
    }  
}
```
- `function getTitle(): string` Diese Methode soll den Titel des Adders zurückgeben
- `function getSchema(): array` Diese Methode soll das Schema des Adders zurückgeben
- `addCustomFieldInRepeater(array $data, Get $get, $set): void` Diese Methode ist vordefiniert und mit dieser können Sie ein neues Feld einfach hinzufügen.
    - `$data` Felddaten für das zu hinzufügende Feld, dort sollte mindesten der `type` und den `identify_key` gesetzt werden. (Wie oben gezeigt)  </br>

#### 11.05.02.01 Registrieren
1. Gehe in die Config `ffhs_custom_forms.php`
2. Fügen sie den `FormEditorFieldAdder` bei `editor_field_adder` hinzu
```php
return [
	'editor_field_adder' => [  
	    GeneralFieldAdder::class,  
	    TemplateAdder::class,  
	    CustomFieldAdder::class,
	    FormEditorFieldAdder::class // <================
	],
];
```
</br>

# 12 Bilder und Dokumente Layoutfelder
## 12.00 Feldeinstellungen in der Config
### 12.00.00 Erklärung
//ToDo
</br>


## 12.01 Bilder und Dokumente Layout-Felder
### 12.01.00 Erklärung
//Speicherung
</br>

### 12.01.1 Einstellungen
//Disk
//save_path
</br>
