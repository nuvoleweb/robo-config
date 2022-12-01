<?php

namespace NuvoleWeb\Robo\Task\Config\Php;

/**
 * Class to prepend configuration.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
class PrependConfiguration extends BaseConfiguration {

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
