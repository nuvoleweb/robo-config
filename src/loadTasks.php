<?php

namespace NuvoleWeb\Robo\Task\Config;

use Robo\Config\Config;
use Robo\Robo;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Consolidation\Config\Loader\YamlConfigLoader;
use Consolidation\Config\Loader\ConfigProcessor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class loadTasks.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
trait loadTasks {

  /**
   * Append Robo YAML configuration to given PHP file as a PHP array.
   *
   * @param string $filename
   *   File path to append Robo configuration to.
   * @param \Robo\Config\Config|null $config
   *   Robo configuration.
   *
   * @return \NuvoleWeb\Robo\Task\Config\AppendConfiguration
   *   Append configuration task.
   */
  protected function taskAppendConfiguration($filename, Config $config = NULL) {
    $config = $config ? $config : Robo::config();
    return $this->task(AppendConfiguration::class, $filename, $config);
  }

  /**
   * Prepend Robo YAML configuration to given PHP file as a PHP array.
   *
   * @param string $filename
   *   File path to prepend Robo configuration to.
   * @param \Robo\Config\Config|null $config
   *   Robo configuration.
   *
   * @return \NuvoleWeb\Robo\Task\Config\AppendConfiguration
   *   Append configuration task.
   */
  protected function taskPrependConfiguration($filename, Config $config = NULL) {
    $config = $config ? $config : Robo::config();
    return $this->task(PrependConfiguration::class, $filename, $config);
  }

  /**
   * Prepend Robo YAML configuration to given PHP file as a PHP array.
   *
   * @param string $filename
   *   Destination file path.
   * @param \Robo\Config\Config|null $config
   *   Robo configuration.
   *
   * @return \NuvoleWeb\Robo\Task\Config\AppendConfiguration
   *   Append configuration task.
   */
  protected function taskWriteConfiguration($filename, Config $config = NULL) {
    $config = $config ? $config : Robo::config();
    return $this->task(WriteConfiguration::class, $filename, $config);
  }

  /**
   * Add default options.
   *
   * @param \Symfony\Component\Console\Command\Command $command
   *   Command object.
   *
   * @hook option
   */
  public function defaultOptions(Command $command) {
    $command->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Configuration file to be used instead of default `robo.yml.dist`.', 'robo.yml');
    $command->addOption('override', 'o', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Configuration value(s) to be overridden. Format: "path.to.key:value"', []);
  }

  /**
   * Command initialization.
   *
   * @param \Symfony\Component\Console\Input\Input $input
   *   Input object.
   *
   * @hook pre-init
   */
  public function initializeConfiguration(Input $input) {

    // Initialize configuration objects.
    $config = Robo::config();
    $loader = new YamlConfigLoader();
    $processor = new ConfigProcessor();

    // Extend and import configuration.
    $processor->add($config->export());
    $processor->extend($loader->load('robo.yml.dist'));
    $processor->extend($loader->load($input->getOption('config')));

    // Replace tokens in final configuration file.
    $export = $processor->export();
    // Load configuration with unprocessed tokens just to be able to access
    // the static values as dot chained names.
    $config->import($export);
    array_walk_recursive($export, function (&$value, $key) use ($config) {
      if (is_string($value)) {
        preg_match_all('/![A-Za-z_\-.]+/', $value, $matches);
        foreach ($matches[0] as $match) {
          $config_key = substr($match, 1, strlen($match));
          $value = str_replace($match, $config->get($config_key), $value);
        }
      }
    });
    // Reimport the config, this time with the tokens replaced.
    $config->import($export);

    // Process command line overrides.
    foreach ($input->getOption('override') as $override) {
      $override = (array) Yaml::parse($override);
      $config->set(key($override), array_shift($override));
    }
  }

  /**
   * Fetch a configuration value.
   *
   * @param string $key
   *   Which config item to look up.
   *
   * @return mixed
   *   Configuration value.
   */
  protected function config($key) {
    return Robo::config()->get($key);
  }

}
