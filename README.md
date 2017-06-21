# Robo Config

[![Build Status](https://travis-ci.org/nuvoleweb/robo-config.svg?branch=master)](https://travis-ci.org/nuvoleweb/robo-config)

Robo Config enables a flexible configuration processing for Robo by providing the following features:

- Define your default configuration in `robo.yml.dist` and let developers override that locally in their `robo.yml`.
- Allow configuration files to use properties defined within the same configuration in a Phing-like fashion.
- Allow all properties to be overridden on the command line so that they can be tweaked when running continuous
  integration builds.
- Access any configuration parameter via `$this->config('my.configuration.property`);`

## Usage

After installation add the following trait to your `RoboFile.php`:

```php
<?php
class RoboFile extends Robo\Tasks {
  
  use NuvoleWeb\Robo\Task\Config\loadTasks;

}
```

## Example

Consider having the following `robo.yml.dist` file:

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

## Use Robo configuration in PHP files

Robo Config exposes a task that allows to append a subset of the YAML configuration to a PHP configuration file,
in a form of a PHP array.

This might be useful for applications that have all of part of their configuration expressed as PHP files,
like [Drupal](http://drupal.org) or [Silex](https://silex.sensiolabs.org).

For example, given the following YAML portion:

```yaml
settings:
  config:
    system.logging:
      error_level: verbose
  settings:
    extension_discovery_scan_tests: TRUE
  config_directories:
    sync: ../config/sync
```

By calling:

```php
<?php
class RoboFile {

  public function myTask() {
    $this->taskAppendConfiguration('/my/config.php')->run();  
  }

}    
```

The content of `settings:` will be rendered as a PHP array and appended to `/my/config.php` as follows:

```php
<?php

// Content of /my/config.php here...

// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["extension_discovery_scan_tests"] = true;

$config_directories["sync"] = '../config/sync';

// End settings processor block.
```

You can configure the task as follow:

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
