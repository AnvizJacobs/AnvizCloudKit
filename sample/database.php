<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 18:02
 * File Name: database.php
 */

/** Connect the database */
$conn = mysql_connect($config['db']['host'], $config['db']['username'], $config['db']['password']);
if (!$conn) {
    die('The Database can not connect!');
}
mysql_select_db($config['db']['dbname'], $conn);
mysql_query("set names 'utf8'");