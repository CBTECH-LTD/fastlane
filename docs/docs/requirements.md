---
id: requirements
title: Requirements
sidebar_label: Requirements
---

<p className="featured">
    Fastlane is built as a Laravel package, a modern and famous PHP framework, and has its
    same server requirements.
</p>

## Server requirements

To run Fastlane you’ll need a server meeting the following requirements. These requirements
are pretty standard in most hosting platforms.

+ PHP `>= 7.4.*'
+ BCMath PHP Extension
+ Ctype PHP Extension
+ JSON PHP Extension
+ Mbstring PHP Extension
+ OpenSSL PHP Extension
+ PDO PHP Extension
+ Tokenizer PHP Extension
+ XML PHP Extension
+ GD Library or ImageMagick

:::caution PHP Version
Ensure your server has PHP 7.4 installed, as Fastlane uses cool stuff that you won't find
on previous PHP versions.
:::

## Development requirements

As Fastlane has requirements similar to Laravel, you can run it on any ordinary Laravel-ready
environment. Laravel Homestead and Laravel Valet are options provided by Laravel itself.

Although not so easy to get started with, we recommend you to use the Docker-based development
environment provided by us.

### Docker

We provide a ready-to-go Docker setup with essential software plus additional perks:

- Nginx with php-fpm
- PostgreSQL
- Redis
- Minio
- Chrome driver

After having installed Fastlane in an existent project, just copy the file:

```shell
mkdir docker
cp vendor/cbtech-ltd/fastlane/docker/docker-compose.yml ./docker
cp -r vendor/cbtech-ltd/fastlane/docker/nginx ./docker/nginx
```

Head to [Getting Started on Fastlane With Docker](#docker) for more information.
