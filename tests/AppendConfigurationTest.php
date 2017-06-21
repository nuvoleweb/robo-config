<?php

namespace NuvoleWeb\Robo\Tests;

use NuvoleWeb\Robo\Task\Config\AppendConfiguration;
use PHPUnit\Framework\TestCase;
use Robo\Config\Config;
use Robo\Config\YamlConfigLoader;

/**
 * Class AppendConfigurationTest.
 *
 * @package NuvoleWeb\Robo\Tests
 */
class AppendConfigurationTest extends TestCase {

  /**
   * Test setting processing.
   *
   * @dataProvider settingsProvider
   */
  public function testProcess($config_file, $source_file, $processed_file) {
    $filename = $this->getFixturePath($source_file);
    $processor = new AppendConfiguration($this->getConfig($config_file), $filename);
    $content = file_get_contents($filename);
    $processed = $processor->process($content);
    $this->assertEquals($processed, trim(file_get_contents($this->getFixturePath($processed_file))));
  }

  /**
   * Data provider.
   *
   * @return array
   *    Test data.
   */
  public function settingsProvider() {
    return [
      ['1-config.yml', '1-input.php', '1-output.php'],
      ['2-config.yml', '2-input.php', '2-output.php'],
    ];
  }

  /**
   * Get configuration object from given fixture.
   *
   * @param string $fixture
   *    Fixture file name.
   *
   * @return \Robo\Config\Config
   *    Configuration object.
   */
  private function getConfig($fixture) {
    $config = new Config();
    $loader = new YamlConfigLoader();
    $loader->load($this->getFixturePath($fixture));
    $config->import($loader->export());
    return $config;
  }

  /**
   * Get fixture file path.
   *
   * @param string $name
   *    Fixture file name.
   *
   * @return string
   *    Fixture file path.
   */
  private function getFixturePath($name) {
    return realpath(dirname(__FILE__) . '/fixtures/' . $name);
  }

}
