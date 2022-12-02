# Robo Config

[![Latest Stable Version](https://poser.pugx.org/nuvoleweb/robo-config/v/stable)](https://packagist.org/packages/nuvoleweb/robo-config)
[![Total Downloads](https://poser.pugx.org/nuvoleweb/robo-config/downloads)](https://packagist.org/packages/nuvoleweb/robo-config)
[![Latest Unstable Version](https://poser.pugx.org/nuvoleweb/robo-config/v/unstable)](https://packagist.org/packages/nuvoleweb/robo-config)
[![License](https://poser.pugx.org/nuvoleweb/robo-config/license)](https://packagist.org/packages/nuvoleweb/robo-config)

Robo Config enables a flexible configuration processing for Robo by providing the following features:

- Define your default configuration in `robo.yml.dist` and let developers override that locally in their `robo.yml`.
- Allow configuration files to use properties defined within the same configuration in a Phing-like fashion.
- Allow all properties to be overridden on the command line so that they can be tweaked when running continuous
  integration builds.
- Access any configuration parameter via `$this->config('my.configuration.property`);`

## Installation

Install with Composer by running:

```
$ composer require nuvoleweb/robo-config
```

## Usage

After installation add the following trait to your `RoboFile.php`:

```php
<?php
class RoboFile extends Robo\Tasks {
  
  use NuvoleWeb\Robo\Task\Config\loadTasks;

}
```

For example, consider having the following `robo.yml.dist` file:

```yml
site:
  name: "Default site name"
  email: "me@example.com"
  url: "http://localhost"
account:
  name: "admin"
  password: !account.name
  email: !site.email
```

And the following `robo.yml` file:

```yml
site:
  name: "My site name"
```

When running:

```
./vendor/bin/robo my-command -o "site.url: http://127.0.0.1:8888"
```

The resulting configuration will be:

```yml
site:
  name: "My site name"
  email: "me@example.com"
  url: "http://127.0.0.1:8888"
account:
  name: "admin"
  password: "admin"
  email: "me@example.com"
```

## Robo configuration in PHP files

Robo Config exposes three tasks that allow to convert a YAML configuration subset into PHP array.
Such array will be then appended, prepended or written down to a PHP destination file as an array.

This might be useful for applications that have part of their configuration expressed in a PHP file,
like [Drupal](http://drupal.org) or [Silex](https://silex.sensiolabs.org).

For example, the following YAML portion:

```yaml
settings:
  config:
    system.logging:
      error_level: verbose
  settings:
    scan_tests: TRUE
```

Will be converted into:

```php
// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["scan_tests"] = true;

// End settings processor block.
```

And added to a PHP file.

### Append task

Given an existing `/my/config.php` file, by calling:

```php
<?php
class RoboFile {

  public function appendTask() {
    $this->taskAppendConfiguration('/my/config.php')->run();  
  }

}    
```

We will get the following result: 

```php
<?php

// Content of /my/config.php here...

// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["scan_tests"] = true;

// End settings processor block.
```

### Prepend task

Given an existing `/my/config.php` file, by calling:

```php
<?php
class RoboFile {

  public function appendTask() {
    $this->taskPrependConfiguration('/my/config.php')->run();  
  }

}    
```

We will get the following result: 

```php
<?php

// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["scan_tests"] = true;

// End settings processor block.

// Content of /my/config.php here...

```

### Write task

Given a non-existing `/my/config.php` file, by calling:

```php
<?php
class RoboFile {

  public function appendTask() {
    $this->taskWriteConfiguration('/my/config.php')->run();  
  }

}    
```

We will get the following result: 

```php
<?php

// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["scan_tests"] = true;

// End settings processor block.

```

### Configure tasks

The behaviors of all tasks above can be customized as follow:

```php
<?php
class RoboFile {

  public function myTask() {
    $config = $this->getMyConfiguration();
    
    $this->taskAppendConfiguration('/my/config.php', $config) // Use custom configuration.
    ->setBlockStart('// Start')                               // Change opening comment.
    ->setBlockEnd('// End')                                   // Change closing comment.
    ->setConfigKey('parameters')                              // Use `parameters:` instead of default `settings:`
    ->run();
  }

}    
```

### Standalone usage

If you just want to use the PHP configuration file processing tasks above in your custom Robo application you can load
them by including the following trait:

```php
<?php

class RoboFile {
  
  use \NuvoleWeb\Robo\Task\Config\Php\loadTasks;
  
}
```

test.