---
id: entry-types
title: Entry Types
---

Entry types define the content of a website managed by the CMS. You can create an entry type running
the command `fastlane:entry-types:make {name}`. You can also provide some additional options:

```
-m | --model: Create the related model.
-i | --migration: Create the related migration.
-s | --schema: Create the related schema.
-p | --policy: Create the related model policy.
-r | --resource: Create the related resource.
--all: Enable all options.
``` 

For instance, to create an Office entry type, run the command:

```sh
php artisan fastlane:entry-types:make Office --all
```

The generated file at `app/EntryTypes/Office/OfficeEntryType.php` is fairly simple as it will try to
guess all things it depends upon.

## Entry description

By default, the entry type will guess its name and its identifier by converting its class name, except
the suffix `EntryType`. In the given example, its name would be _Office_, its identifier would be _offices_.
You can customize it by overriding the following methods `public function name(): string` and
`public function identifier(): string`. 

The plural name of the entry will be guessed from the value returned by the `name` method, but you are
free to override it as well.

You can set an icon to be displayed in the Control Panel by overriding the following method:

```php
{
    public function icon(): string
    {
        return 'building';
    }
}
```

> Currently, Fastlane uses [Line Awesome](https://icons8.com/line-awesome) icons.

## Adding the entry type to your application

After creating the entry type, you will need to add it to the `config/fastlane.php` file:

```php
    /**
     * Enabled Entry Types.
     */
    'entry_types'              => [
        // ...
        \App\EntryTypes\Office\OfficeEntryType::class,
    ],
```

Then, finally run the command `php artisan fastlane:entry-types:install` to install it. The command
will call the `public function install(): void` method defined in the entry type.

## Roles and Permissions

It's not uncommon to need custom roles and permissions in your entry type. The entry type will automatically
generate them in the database according to the class constants you have defined in the class.

> Roles and permissions are automatically generated when the public method `install(): void` is called.
> Fastlane is smart enough to only create roles and permissions when they are not found in the database. 

Let's say our `OfficeEntryType` requires permissions to make a cup of coffee. All you have to do is to define
a constant starting with `PERM_`:

```php
class Office extends EntryType
{
    const PERM_MAKE_COFFEE = 'make coffee';
}
```    

If you need a custom role, the process is pretty the same, just start the constant name with `ROLE_`:

```php
class Office extends EntryType
{
    const ROLE = 'brewer';
}
```

**IMPORTANT NOTE**

Fastlane automatically creates roles and permissions, but it doesn't set what permissions a role has.
To define permissions for a given role, you will need to override one or more of the available
installation methods:

```php
    /**
     * This method simply calls installPermissions and then installRoles.
     */
    protected function installRolesAndPermissions(): void;
    
    /**
     * Install roles according to class constants.
     */
    protected function installRoles(): void;

    /**
     * Install permissions according to class constants.
     */
    protected function installPermissions(): void;
```

> Check the default implementation to find out how to create manually your roles and permissions.

## Model

The generated entry type expects, by default, to find its related model under a file with same name
excluding the suffix EntryType (eg. `app/EntryTypes/Office/Office.php`).

If your model has been saved in another place, you have to override the `public function model(): string` method:    

```php
    public function model(): string
    {
        return \App\Models\Office::class;
    }
```

> Although not a requirement, it's recommended that your models extend `CbtechLtd\Fastlane\Support\Eloquent\BaseModel`
> as it includes Hash IDs, active status management and activity logging. 

## Model Policy

The entry type expects to find the policy related to its model under the same namespace, with `Policy` appended
to the base name of the entry type (eg. `app/EntryTypes/Office/OfficePolicy.php`).  If you want to customize its location,
override the `public function policy(): string` method. 

## Resource Transformer

The entry type expects to find the related JSON Resource transformer under the same namespace, with `Resource` appended
to the base name of the entry type (eg. `app/EntryTypes/Office/OfficeResource.php`). If you want to customize its location,
override the `public function apiResource(): string` method.

## Entry Schema

The entry type expects to find the related entry schema class under the same namespace, with `Schema` appended
to the base name of the entry type (eg. `app/EntryTypes/Office/OfficeSchema.php`). If you want to customize its location,
override the `public function schema(): EntrySchema` method. Please pay attention to the fact that this method
expects an instance of the schema, not a string representing the class name. 
