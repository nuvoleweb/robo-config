<?php

namespace NuvoleWeb\Robo\Task\Config\Php;

use Robo\Config\Config;
use Robo\Exception\TaskException;
use Robo\Task\File\Write;

/**
 * Class BaseConfiguration.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
abstract class BaseConfiguration extends Write implements BaseConfigurationInterface {

  /**
   * Robo configuration object.
   *
   * @var \Robo\Config\Config
   */
  protected $configObject;

  /**
   * Root key in YAML configuration file.
   *
   * @var string
   */
  protected $configKey = 'settings';

  /**
   * Comment starting settings processor block.
   *
   * @var string
   */
  protected $blockStart = "// Start settings processor block.";

  /**
   * Comment ending settings processor block.
   *
   * @var string
   */
  protected $blockEnd = '// End settings processor block.';

  /**
   * BaseConfiguration constructor.
   */
  public function __construct($filename, Config $config) {
    parent::__construct($filename);
    $this->configObject = $config;
  }

  /**
   * Set ConfigKey property.
   *
   * @param mixed $config_key
   *   Property value.
   *
   * @return $this
   */
  public function setConfigKey($config_key) {
    $this->configKey = $config_key;
    return $this;
  }

  /**
   * Set BlockStart property.
   *
   * @param mixed $block_start
   *   Property value.
   *
   * @return $this
   */
  public function setBlockStart($block_start) {
    $this->blockStart = $block_start;
    return $this;
  }

  /**
   * Set BlockEnd property.
   *
   * @param mixed $block_end
   *   Property value.
   *
   * @return $this
   */
  public function setBlockEnd($block_end) {
    $this->blockEnd = $block_end;
    return $this;
  }

  /**
   * Get configuration block.
   *
   * @throws \Robo\Exception\TaskException
   *    Thrown when configuration key does not exists.
   */
  protected function getConfigurationBlock() {
    $line[] = '';
    $line[] = $this->blockStart;
    $line[] = '';

    if (!$this->configObject->has($this->configKey)) {
      throw new TaskException($this, "Configuration key '{$this->configKey}' not found on current Robo configuration.");
    }

    foreach ($this->configObject->get($this->configKey) as $variable => $values) {
      foreach ($values as $name => $value) {
        $line[] = $this->getStatement($variable, $name, $value);
      }
      $line[] = '';
    }

    $line[] = $this->blockEnd;
    $line[] = '';
    return implode($line, "\n");
  }

  /**
   * {@inheritdoc}
   */
  protected function getContentsToWrite() {
    $content = $this->originalContents();
    return $this->process($content);
  }

  /**
   * Remove settings block from given content.
   *
   * @param string $content
   *   Content of a PHP file.
   *
   * @return string
   *   Content without setting block.
   */
  protected function sanitizeContent($content) {
    $regex = "/^" . preg_quote($this->blockStart, '/') . ".*?" . preg_quote($this->blockEnd, '/') . "/sm";
    return preg_replace($regex, '', $content);
  }

  /**
   * Get variable assignment statement.
   *
   * @param string $variable
   *   Variable name.
   * @param string $name
   *   Setting name.
   * @param mixed $value
   *   Setting value.
   *
   * @return string
   *   Full statement.
   */
  protected function getStatement($variable, $name, $value) {
    $output = var_export($value, TRUE);
    if (is_array($value)) {
      $output = str_replace(' ', '', $output);
      $output = str_replace("\n", "", $output);
      $output = str_replace("=>", " => ", $output);
      $output = str_replace(",)", ")", $output);
      $output = str_replace("),", "), ", $output);
    }
    return sprintf('$%s["%s"] = %s;', $variable, $name, $output);
  }

}
