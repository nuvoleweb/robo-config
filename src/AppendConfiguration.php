<?php

namespace NuvoleWeb\Robo\Task\Config;

use Robo\Config\Config;
use Robo\Task\File\Write;

/**
 * Class AppendConfiguration.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
class AppendConfiguration extends Write {

  /**
   * Robo configuration object.
   *
   * @var \Robo\Config\Config
   */
  protected $config;

  /**
   * Root key in YAML configuration file.
   *
   * @var string
   */
  private $configKey = 'settings';

  /**
   * Comment starting settings processor block.
   *
   * @var string
   */
  private $blockStart = "// Start settings processor block.";

  /**
   * Comment ending settings processor block.
   *
   * @var string
   */
  private $blockEnd = '// End settings processor block.';

  /**
   * AppendConfiguration constructor.
   */
  public function __construct($filename, Config $config) {
    parent::__construct($filename);
    $this->config = $config;
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
   * Process settings file.
   *
   * @param string $content
   *   Content of a PHP file.
   *
   * @return string
   *   Processed setting file.
   */
  public function process($content) {
    $line[] = $this->sanitizeContent($content);
    $line[] = '';
    $line[] = $this->blockStart;
    $line[] = '';

    foreach ($this->config->get($this->configKey) as $variable => $settings) {
      foreach ($settings as $name => $value) {
        $line[] = $this->getStatement($variable, $name, $value);
      }
      $line[] = '';
    }

    $line[] = $this->blockEnd;
    $content = implode($line, "\n");

    return $content;
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
  private function sanitizeContent($content) {
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
  private function getStatement($variable, $name, $value) {
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
