<?php

namespace NuvoleWeb\Robo\Tests;

use League\Container\ContainerAwareInterface;
use NuvoleWeb\Robo\Task\Config\AppendConfiguration;
use NuvoleWeb\Robo\Task\Config\loadTasks;
use PHPUnit\Framework\TestCase;
use Robo\Config\Config;
use Consolidation\Config\Loader\YamlConfigLoader;
use League\Container\ContainerAwareTrait;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Robo\TaskAccessor;
use Robo\Robo;
use Robo\Collection\CollectionBuilder;

/**
 * Class TaskTest.
 *
 * @package NuvoleWeb\Robo\Tests
 */
class TaskTest extends TestCase implements ContainerAwareInterface {

  use loadTasks;
  use TaskAccessor;
  use ContainerAwareTrait;

  /**
   * {@inheritdoc}
   */
  function setup() {
    $container = Robo::createDefaultContainer(null, new NullOutput());
    $this->setContainer($container);
  }

  /**
   * Tests token replacement.
   *
   * @covers \NuvoleWeb\Robo\Task\Config\loadTasks::initializeConfiguration
   */
  public function testTokenReplacement() {
    $definition = new InputDefinition([
      new InputOption('config', 'c', InputOption::VALUE_REQUIRED),
      new InputOption('override', 'o', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL),
    ]);

    $input = new StringInput("--config='{$this->getFixturePath('config-with-tokens.yml')}'");
    $input->bind($definition);
    $this->initializeConfiguration($input);

    // Check that static configurations are correct.
    $this->assertEquals('bar', $this->config('foo'));
    $this->assertEquals('some_value', $this->config('bar.baz'));
    // Check that tokens were resolved.
    $this->assertEquals('bar', $this->config('qux.var1'));
    $this->assertEquals('some_value', $this->config('qux.var2'));
  }

  /**
   * Scaffold collection builder.
   *
   * @return \Robo\Collection\CollectionBuilder
   *    Collection builder.
   */
  public function collectionBuilder() {
    $empty_robo_file = new \Robo\Tasks;
    return CollectionBuilder::create($this->getContainer(), $empty_robo_file);
  }

  /**
   * Test task run.
   *
   * @dataProvider appendTestProvider
   */
  public function testTaskAppendConfiguration($config_file, $source_file, $processed_file) {
    $source = $this->getFixturePath($source_file);
    $filename = $this->getFixturePath('tmp/' . $source_file);
    copy($source, $filename);

    $config = $this->getConfig($config_file);
    $command = $this->taskAppendConfiguration($filename, $config)->run();
    $this->assertNotEmpty($command);
    $this->assertEquals(trim(file_get_contents($filename)), trim(file_get_contents($this->getFixturePath($processed_file))));
  }

  /**
   * Test task run.
   *
   * @dataProvider prependTestProvider
   */
  public function testTaskPrependConfiguration($config_file, $source_file, $processed_file) {
    $source = $this->getFixturePath($source_file);
    $filename = $this->getFixturePath('tmp/' . $source_file);
    copy($source, $filename);

    $config = $this->getConfig($config_file);
    $command = $this->taskPrependConfiguration($filename, $config)->run();
    $this->assertNotEmpty($command);
    $this->assertEquals(trim(file_get_contents($filename)), trim(file_get_contents($this->getFixturePath($processed_file))));
  }

  /**
   * Test task run.
   *
   * @dataProvider writeTestProvider
   */
  public function testTaskWriteConfiguration($config_file, $processed_file) {
    $filename = $this->getFixturePath('tmp/' . $processed_file);
    $config = $this->getConfig($config_file);
    $command = $this->taskPrependConfiguration($filename, $config)->run();
    $this->assertNotEmpty($command);
    $this->assertEquals(trim(file_get_contents($filename)), trim(file_get_contents($this->getFixturePath($processed_file))));
  }

  /**
   * Test setting processing.
   *
   * @dataProvider appendTestProvider
   */
  public function testProcess($config_file, $source_file, $processed_file) {
    $filename = $this->getFixturePath($source_file);
    $config = $this->getConfig($config_file);

    $processor = new AppendConfiguration($filename, $config);
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
  public function appendTestProvider() {
    return [
      ['1-config.yml', '1-input.php', '1-output-append.php'],
      ['2-config.yml', '2-input.php', '2-output-append.php'],
    ];
  }

  /**
   * Data provider.
   *
   * @return array
   *    Test data.
   */
  public function prependTestProvider() {
    return [
      ['1-config.yml', '1-input.php', '1-output-prepend.php'],
      ['2-config.yml', '2-input.php', '2-output-prepend.php'],
    ];
  }


  /**
   * Data provider.
   *
   * @return array
   *    Test data.
   */
  public function writeTestProvider() {
    return [
      ['3-config.yml', '3-output-write.php'],
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
    return dirname(__FILE__) . '/fixtures/' . $name;
  }

}
