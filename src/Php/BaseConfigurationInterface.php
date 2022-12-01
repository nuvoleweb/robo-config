<?php

namespace NuvoleWeb\Robo\Task\Config\Php;

/**
 * Interface of the BaseConfigurationInterface class.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
interface BaseConfigurationInterface {

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
