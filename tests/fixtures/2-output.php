<?php

/**
 * @file
 * Settings file fixture.
 */

/**
 * Load local development override configuration, if available.
 *
 * Use settings.local.php to override variables on secondary (staging,
 * development, etc) installations of this site. Typically used to disable
 * caching, JavaScript/CSS compression, re-routing of outgoing emails, and
 * other things that should not happen on development and testing sites.
 *
 * Keep this code block at the end of this file to take full effect.
 */
#
# if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
#   include $app_root . '/' . $site_path . '/settings.local.php';
# }


// Start settings processor block.

$config["system.performance"] = array('css' => array('preprocess' => false), 'js' => array('preprocess' => false));

$settings["file_public_path"] = 'files/public';
$settings["file_private_path"] = 'files/private';
$settings["container_yamls"] = array(0 => 'resources/services.yml');
$settings["file_scan_ignore_directories"] = array(0 => 'node_modules',1 => 'bower_components');

// End settings processor block.
