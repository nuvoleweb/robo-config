<?php

namespace NuvoleWeb\Robo\Task\Config;

/**
 * Class WriteConfiguration.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
class WriteConfiguration extends BaseConfiguration {

  /**
   * {@inheritdoc}
   */
  public function process($content) {
    return "<?php\n" . $this->getConfigurationBlock();
  }

}
