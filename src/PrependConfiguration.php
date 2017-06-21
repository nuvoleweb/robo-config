<?php

namespace NuvoleWeb\Robo\Task\Config;

/**
 * Class PrependConfiguration.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
class PrependConfiguration extends WriteConfiguration {

  /**
   * {@inheritdoc}
   */
  public function process($content) {
    $content = $this->sanitizeContent($content);
    return "<?php\n" . $this->getConfigurationBlock() . $content;
  }

  /**
   * {@inheritdoc}
   */
  protected function sanitizeContent($content) {
    $content = parent::sanitizeContent($content);
    return preg_replace('/^<\?(php)?/', '', $content);
  }

}
