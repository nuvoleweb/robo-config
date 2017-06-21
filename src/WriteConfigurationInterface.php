<?php

namespace NuvoleWeb\Robo\Task\Config;

/**
 * Interface WriteConfigurationInterface.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
interface WriteConfigurationInterface {

  /**
   * Process settings file.
   *
   * @param string $content
   *   Content of a PHP file.
   *
   * @return string
   *   Processed setting file.
   */
  public function process($content);

}
