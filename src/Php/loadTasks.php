<?php

namespace NuvoleWeb\Robo\Task\Config\Php;

use Robo\Config\Config;
use Robo\Robo;

/**
 * Class to load PHP tasks.
 *
 * @package NuvoleWeb\Robo\Task\Config\Php
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
   * @return \NuvoleWeb\Robo\Task\Config\Php\AppendConfiguration
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
   * @return \NuvoleWeb\Robo\Task\Config\Php\AppendConfiguration
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
   * @return \NuvoleWeb\Robo\Task\Config\Php\AppendConfiguration
   *   Append configuration task.
   */
  protected function taskWriteConfiguration($filename, Config $config = NULL) {
    $config = $config ? $config : Robo::config();
    return $this->task(WriteConfiguration::class, $filename, $config);
  }

}
