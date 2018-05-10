<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 16:08
 * File Name: Webserver.php
 */

require(dirname(__FILE__) . '/../config.php');
require(dirname(__FILE__) . '/../database.php');
require(dirname(__FILE__) . '/../../src/Webserver.php');
require(dirname(__FILE__) . '/callback.php');

/** Callback function */
$callback = new callback();

/** Run SOAP Server */
$server = new Webserver($config);
$server->run($callback);