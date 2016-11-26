# Schema Builder Package

Schema builder for building models, migrations, etc from schema

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/schemabuilder": "^0.1.0"
```

1.3 To use with Laravel add

```php
    '\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider',
```

under 'providers' key in app/config/app.php file

or

```php
if (class_exists('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider')) {
    App::register('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider');
}
```

in app/start/artisan.php for conditional registration

## Usage

1. Put your schema.yml into config/schemabuilder directory of your project
(or modify respective constant of BuildCommandAbstract).
You may also use doc/examples/schema.yml and modify it as needed.

2. If needed, put AbstractModel from doc/examples into your project

3. Setup autoload for needed classes and namespaces in your composer.json and run
```bash
composer dump-autoload
```

4. Run from command line

```bash
php artisan build
```

