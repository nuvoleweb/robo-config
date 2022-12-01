<?php

namespace NuvoleWeb\Robo\Task\Config;

/**
 * Includes provided loadTasks classes.
 *
 * @package NuvoleWeb\Robo\Task\Config
 */
trait loadTasks {

  use \NuvoleWeb\Robo\Task\Config\Php\loadTasks;
  use \NuvoleWeb\Robo\Task\Config\Robo\loadTasks;

}
