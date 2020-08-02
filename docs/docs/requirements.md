---
id: requirements
title: Requirements
sidebar_label: Requirements
---

<p className="featured">
    Fastlane is built as a Laravel package, a modern and famous PHP framework, and has
    the same server requirements as it. These requirements may change depending on plugins
    you install and functionalities your website needs.
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

Although not so easy to get started with, we recommend you to use the development environment
provided by us. We provide Docker and Kubernetes setups. They are basically the same thing,
just used in distinguished ways. 

### Docker

Fastlane provides a ready-to-go `docker-compose.yml` in the root directory of new projects.
If you've installed Fastlane in an existent project, just copy the file:

```shell
cp vendor/cbtech-ltd/fastlane/docker-compose.yml ./
```

Head to [Getting Started on Fastlane With Docker](#docker) for more information.

### Kubernetes with DevSpace

We have been using [DevSpace](https://devspace.sh/) successfully to create dev
and production servers. Some developers use it with local `minikube`, others
work directly on hosted Kubernetes clusters.

[You can read more about setting it up here](#k8s).
