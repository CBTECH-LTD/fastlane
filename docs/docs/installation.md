---
id: installation
title: Installation
sidebar_label: Installation
---

...

## New project?

Create a Laravel project as you are used to do:

```shell
$ composer create-project --prefer-dist laravel/laravel {PROJECT_NAME}
``` 

Follow the next steps.


## Adding to an existing Laravel app

Run the following command to add Fastlane to your project:

```shell
$ composer require cbtech-ltd/fastlane
```

After the install, publish the configuration file and Fastlane assets:

```shell
$ php artisan vendor:publish --tag=fastlane-config
$ php artisan vendor:publish --tag=fastlane-assets
```

## Next steps
