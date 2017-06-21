<?php

/**
 * @file
 * Settings file fixture.
 */

$settings = [
  'one' => 'two',
  'three' => 'four',
];




// Start settings processor block.

$config["system.logging"] = array('error_level' => 'verbose');

$settings["extension_discovery_scan_tests"] = true;

$config_directories["sync"] = '../config/sync';

// End settings processor block.
