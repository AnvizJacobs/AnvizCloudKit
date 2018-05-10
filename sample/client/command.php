<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午3:03
 * File Name: command.php
 */

require(dirname(__FILE__) . '/../config.php');
require(dirname(__FILE__) . '/../database.php');
require(dirname(__FILE__) . '/../../src/lib/Tools.php');
require(dirname(__FILE__) . '/../../src/lib/Protocol.php');
require(dirname(__FILE__) . '/../../src/lib/AnvizCommand.php');

$device_id = $_REQUEST['id'];
$command = $_REQUEST['command'];

if (empty($device_id) || empty($command)) {
    echo json_encode(array(
        'success' => false
    ));
    exit;
}

$anvizCommand = new AnvizCommand();

if ($command == 'downloadAllRecords') {

    $data = $anvizCommand->getRecords(0, 100);

    $id = $data['id'];
    $params = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params) 
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    mysql_query($sql);

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
}