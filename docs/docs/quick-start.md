---
id: quick-start
title: Quick Start
sidebar_label: Quick Start
---

...

After cloning the Hewitsons CMS repository using `git clone git@github.com:CBTECH-LTD/hewitsons-cms.git`, you have to check out the `develop` branch (more info on _Git Workflow_).

Then, create a copy of the `.env.example` file located in the root directory to a file called `.env`. The default values are ready to work with the included Docker project, but you're free to edit it according to your development environment.

## Development environment

The project source code includes a complete development environment based on Docker. It's the recommended stack to guarantee everyone gets the same dev environment.

> You need to have Docker and Docker Compose properly installed and running in your system.

First, create a `docker/.env` file based on `docker/env-example`. If you have special needs (eg. ports are already in use), update it accordingly.

### Included containers

- **php-cli**: the container where you must run shell commands, like `yarn`, `composer`, `horizon`, etc.
- **php-fpm**: this container runs the FPM version of PHP 7.4. It does include XDebug.
- **redis**
- **postgresql**
- **nginx**
- **chrome**: this container is used by php-cli to get a instance of Google Chrome for Laravel Dusk.

### Commands

When your `docker/.env` is set, it's time to run the containers. In the root directory of the project, you find a script named `dev.sh` to help you with common commands. It's written to work with Ubuntu SH shell, but you can try it if you're using MacOS. If it doesn't work, you can read it to check what are the commands.

> You can create another file (eg. `dev.os.sh`) with the commands that work for your OS :)

The available commands are:

---

#### up

It runs all containers defined in the `docker/docker-compose.yml` file. If it's the first time you run it, go have a big cup of coffee!

```shell
./dev.sh up
```

---

#### stop

It just stops all the running containers.

```shell
./dev.sh stop
```

---

#### art

If you want to execute any artisan command, you don't need to enter `php-cli` container, just run this command and it will work.

```shell
./dev.sh art {command}
```

---

#### composer

You can require new packages, remove them and run any other command that composer supports.

```shell
./dev.sh composer {command}
```

---

#### yarn

You can run any command supported by yarn. You can, for example, run `./dev.sh yarn watch` to rebuild your front-end assets when something changes.

```shell
./dev.sh yarn {command}
```

---

#### sh

By running this command, you're given access to the `php-cli` shell, so you can perform any action inside the containerized system.

```shell
./dev.sh sh
```

---

#### install

By running this command, you do the initial installation of the project.

```shell
./dev.sh install
```

> Check the dev.sh file to check what actions are performed.

---

#### {anything}

IF NEEDED, you can run any other Shell command. For example: `./dev.sh chmod -R 777 storage/logs`.

```shell
./dev.sh {command}
```

---

## Installation

If you're using the recommended Docker setup, all you need to do is to run:

```shell
./dev.sh install
```

### Using your own dev environment

Check the required `git` and `artisan` commands in the `dev.sh` file.

## Creating your first admin user

When your environment is set up, run the following command to define a new user. You'll be prompted to answer some questions:

```shell
./dev.sh art fastlane:system-admin:make
```
---

## Overview

## Install Fastlane

## The first user

## Make a home page

## Create a car listing page
