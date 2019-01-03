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
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
} elseif ($command == 'downloadAllEmployee') {
    $data = $anvizCommand->getEmployees(0, 100);

    $id = $data['id'];
    $params = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params) 
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
} elseif ($command == 'downloadAllTemplate') {
    $data = $anvizCommand->getFingers(0, 100);

    $id = $data['id'];
    $params = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params) 
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
} elseif ($command == 'clearAllEmployees') {
    $data = $anvizCommand->clearEmployee();

    $id = $data['id'];
    $params = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params) 
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
} elseif ($command == 'uploadAllEmployees') {
    $sql = 'SELECT * FROM employee';
    $query = $db->query($sql);
    while (true) {
        $employee = array();
        $limit = 0;
        while ($row = $db->fetch_array($query)) {
            $employee[] = array(
                'idd' => $row['idd'],
                'passd' => $row['passd'],
                'cardid' => $row['cardid'],
                'name' => $row['name'],
                'group_id' => $row['group_id'],
                'is_admin' => $row['is_admin']
            );
            $limit++;
            if ($limit >= 100) {
                break;
            }
        }
        if (empty($employee))
            break;

        $data = $anvizCommand->setEmployee($employee);
        $id = $data['id'];
        $params = $data['params'];
        $command = $data['command'];
        $content = $data['content'];

        $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
        $db->query($sql);
    }

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'id' => $id
        )
    ));
} elseif ($command == 'uploadAllTemplates') {
    $sql = 'SELECT * FROM employee_template';
    $query = $db->query($sql);
    while (true) {
        $template = array();
        $limit = 0;
        while ($row = $db->fetch_array($query)) {
            $template[] = array(
                'idd' => $row['idd'],
                'sign' => $row['temp_id'],
                'template' => base64_decode($row['content'])
            );
            $limit++;
            if ($limit >= 100) {
                break;
            }
        }
        if (empty($template))
            break;

        $data = $anvizCommand->setFingers($template);
        $id = $data['id'];
        $params = $data['params'];
        $command = $data['command'];
        $content = $data['content'];

        $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
        $db->query($sql);

        echo json_encode(array(
            'success' => true,
            'data' => array(
                'id' => $id
            )
        ));
    }
}