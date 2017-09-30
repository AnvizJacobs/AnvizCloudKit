<?php
/**
 * Created by Jacobs.
 * Auth: jacobs@anviz.com
 * Copyright: Anviz Global Inc.
 * Date: 2017/9/20
 * Time: 13:50
 * FileName: config.php
 */
/**
 * Developer mode
 */
define('KEY', 'AnvizDevelopOpenKey');

/*
 * Debug mode
 * If set true,  a large number of logs will be create in logs folder
 */
define('DEBUG', true);

/*
 * Set the server time zone
 * Reference: http://php.net/manual/en/function.date-default-timezone-set.php
 */
define('TIMEZONE', 'UTC');

/*
 * Configure the callback function
 * CALLBACK_FILE: The absolute path of the file containing callback function
 * CALLBACK_CLASS: The class name
 */
define('CALLBACK_FILE', dirname(__FILE__) . '/../sample/index.php');
define('CALLBACK_CLASS', 'callback');