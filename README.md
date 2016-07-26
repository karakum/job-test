Тестовое задание
================

Необходимо сделать минимально рабочий сервис, без багов и всем указанным функционалом. В приоритете скорость разработки.

Авторизация пользователя по кнопке авторизации, и для авторизации/регистрации достаточно использовать уникальный никнейм для пользователя. Если такого пользователя нет, то создать его автоматически и авторизовать. Также должна быть кнопка выйти. Сделать публичную страницу со списком всех пользователей и их балансом.

Для авторизованных доступно следующее:

Пользователь может перевести любую вымышленную сумму другому пользователю (идентификация по никнейму). При этом баланс пользователя уменьшается на указанную сумму. Баланс может быть отрицательным. Баланс у всех новых пользователей по умолчанию 0. Можно перевести сумму на любой никнейм, даже вымышленный, тогда нужно создать такого пользователя и зачислить ему баланс.

Также пользователь может выставить счет другому пользователю на какую то сумму, и другой пользователь может его оплатить, тогда перевод оплаты мгновенный, либо отказаться от оплаты счеты.

Пользователи должны видеть все свои переводы, счета, их состояния и статусы.

---

По возможности использовать crud для скорости разработки. Использовать для создания/редактирования/удаления обьектов.

Писать на yii2, шаблон basic. База должна устанавливаться только из миграций, вендоры должны устанавливаться только через компосер с минимальной стабильностью stable. Код должен быть оформлен в соответствии со стилем кодирования и структурой каталогов в yii2.

Должны быть написаны все необходимые unit тесты codeception, по возможности и functional.

Код выложите на какой нибудь онлайн репозиторий git. Рабочий сайт выложите по возможности в онлайн чтобы можно было посмотреть результат без установки проекта локально. По завершении работы сообщите сколько было затрачено времени на разработку.

Приоритет в разработке между скоростью и производительностью должен быть в пользу скорости разработки. Помните что неподдерживаемый и непонятный код для других разработчиков снижает скорость разработки. Нужно использовать готовые решения и возможности фреймворка.


Yii 2 Basic Project Template
============================

Yii 2 Basic Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-basic/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-basic/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-basic.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-basic)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.


INSTALLATION
------------

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](http://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
php composer.phar global require "fxp/composer-asset-plugin:~1.1.1"
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.
