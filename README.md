# CustomForms Description

This plugin enables your users to create, fill out, and manage forms in Filament.
It provides a variety of customizable form fields and allows you to adjust the form behavior using validation rules.
The fields can be freely tailored and extended to fit your needs.

## Features

### Form Configurations

Multi-purpose templates: Create distinct configurations for different use cases (e.g., registrations, surveys,
payments).

Extensible rules: Context-specific logic (e.g., hide payment fields in surveys).

### Dynamic Fields

Drag & Drop builder: Intuitive form layout editing.

Repeater fields: Support for repeatable field groups.

Predefined field types:

```php
// Input Fields
TextType::class, EmailType::class, NumberType::class, TextAreaType::class,
DateTimeType::class, DateType::class, DateRangeType::class, FileUploadType::class,

// Choice Fields
SelectType::class, RadioType::class, CheckboxListType::class, ToggleButtonsType::class,

// Special Fields
TagsType::class, KeyValueType::class, ColorPickerType::class, IconSelectType::class,

// Layout Components
SectionType::class, FieldsetType::class, GroupType::class, TitleType::class,
TextLayoutType::class, DownloadType::class, ImageLayoutType::class, SpaceType::class
```

### Rules & Triggers

Conditional logic:

```php
'event' => [
    HideEvent::class,
    VisibleEvent::class,
    DisabledEvent::class,
    RequiredEvent::class,
    ChangeOptionsEvent::class, 
],
'trigger' => [
    IsInfolistTrigger::class,
    ValueEqualsRuleTrigger::class,
    AlwaysRuleTrigger::class,
]
```

Extensible system: Add custom events/triggers.

### Templates & Uniqueness

Reusable templates: Deploy forms across multiple projects.

Mandatory fields: Enforce unique or required fields per configuration.

### View Modes

Context-aware styling: Change field appearance (e.g., blue text fields for specific workflows).

Layout presets: Optimize forms for different use cases (e.g., mobile vs. admin views).

## Images

### Editor

![Editor](.\images\editor.png)
![Editor](.\images\field_options.png)

### Fill CustomForm

![Fill 1](.\images\edit_1.png)

![Fill 2](.\images\edit_2.png)

### Templates

![Template Editor](.\images\template_editor.png)

### Rules

![Template Rules](.\images\template_editor_rules.png)

### Rules

![General Fields](.\images\general_fields.png)

## Installation

You can install the package via composer:

```bash
composer require ffhs/filament-package_ffhs_custom_forms
```

<br>

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag=":filament-package_ffhs_custom_forms-migrations"
php artisan migrate
```

<br>

You can publish the config file with:

```bash
php artisan vendor:publish --tag=":filament-package_ffhs_custom_forms-config"
```

<br>

You can add the resources to your panel with in your PanelProvider:

```php
->plugins([
    CustomFormPlugin::make(),
])
```

<br>

The CustomForm plugins needs [Icon Picker Plugin](https://filamentphp.com/plugins/guava-icon-picker)
You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-icon-picker-config"
```

<br>

You can add to the IconPicker config

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
<br>

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag=":filament-package_ffhs_custom_forms-views"
```

## Testing

```bash
composer test
```

## Usage

A CustomForm consists of:
CustomFormConfiguration – The blueprint defining form behavior, fields, and rules.
CustomFields – Dynamic fields rendered in the form.

Multiple form configurations can be created, each serving different purposes. For example, one configuration might be
designed for registration forms with specific dependencies where only certain rules apply, while another type could be
intended for surveys.

### Creating a Form Configuration

To create a new form, you must first define a Form Configuration by extending the CustomFormConfiguration class.

```php
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\CustomFormConfiguration
class SimpleForm extends CustomFormConfiguration
{
    public static function identifier(): string
    {
        return 'simple_form';
    }

    public static function displayName(): string
    {
        return __(...);
    }
}
```

Key Features:

- `identifier()`  links saved forms to their config version.
- `displayName()`  appears in Filament’s form dropdown.

### Registering a Form Configuration

Newly created configurations must be registered in the ffhs_custom_forms config file:

```php
// config/ffhs_custom_forms.php
'forms' => [
    SimpleForm::class,
],
```

Key Notes:

- Add each configuration class to the forms array.
- Reload the config after change.

