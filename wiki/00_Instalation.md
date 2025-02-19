## 00.00 Hinterlegen des Packages im Composer

composer.json

```json
"require": {
"ffhs/filament-package_ffhs_custom_forms": "dev-main", ...
}
"repositories": {
"dev-custom-forms": {
"type": "path",
"url": "packages/ffhs/filament-package_ffhs_custom_forms"
}
}
```

In config/app.php hinzufügen den `CustomFormServiceProvider`

```php
[
    "providers"=>[
        ...
    ]
]
```

PanelProvide

```php

                new CustomFormPlugin(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['de', 'en']),
```

```bash
composer update
``` 

Können

```bash
php artisan vendor:publish --tag=filament-package_ffhs_custom_forms-views
``` 

<br>

## 00.01 Registrieren des Packets in AdminPanelProvider

Fügen Sie das Plugin zu einem Panel hinzu, indem Sie die Plugin-Klasse instanziieren (
\app\Providers\Filament\AdminPanelProvider.php) und sie an die plugin()-Methode der Konfiguration übergeben:

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

## 00.02 Starten des Installers

Starten Sie den Installer

```bash  
php artisan filament-package_ffhs_custom_forms:install
```

<br>

## 00.03 Setup des Icon-Picker Plugins

- Das Custom Forms Plugin benötigt ein [Icon Picker Plugin](https://v2.filamentphp.com/plugins/icon-picker)
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

## 00.04 Permission Seeder

- Lass den `CustomFormPermissionSeeder` in Ihrem Seeder laufen.

```php
class DatabaseSeeder extends Seeder  
{  
    public function run(): void  
    {    
		(new CustomFormPermissionSeeder())->run();
     }
}
```

<br>

