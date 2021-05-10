<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午3:03
 * File Name: command.php
 */

require dirname(__FILE__) . '/../config.php';
require dirname(__FILE__) . '/../database.php';
require dirname(__FILE__) . '/../../src/lib/Tools.php';
require dirname(__FILE__) . '/../../src/lib/Protocol.php';
require dirname(__FILE__) . '/../../src/lib/AnvizCommand.php';

$device_id = $_REQUEST['id'];
$command   = $_REQUEST['command'];

if (empty($device_id) || empty($command)) {
    echo json_encode(array(
        'success' => false,
    ));
    exit;
}

$anvizCommand = new AnvizCommand();



if ($command == 'downloadNewTemperatureRecords') {

    $data = $anvizCommand->getNewTemperatureRecords(0, 100);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
}elseif ($command == 'setMaskTemperatureConfig') {

    $mask_detection = $_REQUEST['mask_detection'];
    $mask_alarm = $_REQUEST['mask_alarm'];
    $work_mode = $_REQUEST['work_mode'];
    $relay_output = $_REQUEST['relay_output'];
    $temp_unit = $_REQUEST['temp_unit'];
    $fever_threshold = $_REQUEST['fever_threshold']*10;
    $fever_alarm = $_REQUEST['fever_alarm'];
    $temp_opendoor = $_REQUEST['temp_opendoor'];
    $temp_access = $_REQUEST['temp_access'];

    $_data = [
        'mask_detection' => $mask_detection ,
        'mask_alarm' => $mask_alarm ,
        'work_mode' => $work_mode,
        'relay_output' => $relay_output ,
        'temp_unit' => $temp_unit ,
        'fever_threshold' => $fever_threshold ,
        'fever_alarm' => $fever_alarm,
        'temp_opendoor' => $temp_opendoor ,
        'temp_access' => $temp_access ,
    ];

    $data = $anvizCommand->setMaskTemperatureConfig($_data);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
}elseif ($command == 'downloadAllRecords') {

    $data = $anvizCommand->getRecords(0, 100);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'downloadAllEmployee') {
    $data = $anvizCommand->getEmployees(0, 100);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'downloadAllTemplate') {
    $data = $anvizCommand->getFingers(0, 100);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'clearAllEmployees') {
    $data = $anvizCommand->clearEmployee();

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'uploadAllEmployees') {
    $sql   = 'SELECT * FROM employee';
    $query = $db->query($sql);
    while (true) {
        $employee = array();
        $limit    = 0;
        while ($row = $db->fetch_array($query)) {
            $employee[] = array(
                'idd'      => $row['idd'],
                'passd'    => $row['passd'],
                'cardid'   => $row['cardid'],
                'name'     => $row['name'],
                'group_id' => $row['group_id'],
                'is_admin' => $row['is_admin'],
            );
            $limit++;
            if ($limit >= 100) {
                break;
            }
        }
        if (empty($employee)) {
            break;
        }

        $data    = $anvizCommand->setEmployee($employee);
        $id      = $data['id'];
        $params  = $data['params'];
        $command = $data['command'];
        $content = $data['content'];

        $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
        $db->query($sql);
    }

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'uploadAllTemplates') {
    $sql   = 'SELECT * FROM employee_template';
    $query = $db->query($sql);
    while (true) {
        $template = array();
        $limit    = 0;
        while ($row = $db->fetch_array($query)) {
            $template[] = array(
                'idd'      => $row['idd'],
                'sign'     => $row['temp_id'],
                'template' => base64_decode($row['content']),
            );
            $limit++;
            if ($limit >= 100) {
                break;
            }
        }
        if (empty($template)) {
            break;
        }

        $data    = $anvizCommand->setFingers($template);
        $id      = $data['id'];
        $params  = $data['params'];
        $command = $data['command'];
        $content = $data['content'];

        $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
        $db->query($sql);

        echo json_encode(array(
            'success' => true,
            'data'    => array(
                'id' => $id,
            ),
        ));
    }
} elseif ($command == 'importBackFile') {
    if (empty($_FILES) || empty($_FILES['file'])) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }
    $content = file_get_contents($_FILES['file']['tmp_name']);
    if (empty($content)) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }

    $data = Protocol::RecordImport($content);

    $sql    = 'SELECT * FROM device WHERE id="' . $device_id . '"';
    $result = $db->query($sql);
    if ($db->num_rows($result) <= 0) {
        return false;
    }

    $device  = $db->fetch_array($result);
    $user_id = $device['user_id'];

    foreach ($data as $row) {
        $idd       = $row['idd'];
        $checktime = $row['checktime'];

        $sql    = 'SELECT * FROM records WHERE idd="' . $idd . '" AND device_id="' . $device_id . '" AND checktime="' . $checktime . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) > 0) {
            continue;
        }

        $sql = 'INSERT INTO records(idd, device_id, checktime, user_id) VALUES ("' . $idd . '", "' . $device_id . '", "' . $checktime . '", "' . $user_id . '")';
        $db->query($sql);
    }

    header('Location:device.php?id=' . $device_id);
} elseif ($command == 'getRecordUserFPCount') {

    $data = $anvizCommand->getRecordUserFPCount();

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'setSuperAdminPassword') {

    $password = $_REQUEST['password'];

    if (empty($password)) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }
    if(!is_numeric($password)){
        echo json_encode(array(
            'success' => false
        ));
        exit;
    }

    $data = $anvizCommand->setSuperAdminPassword($password);

    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'enrollCard') {
    $idd = $_REQUEST['idd'];

    if (empty($idd)) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }

    $data    = $anvizCommand->setEnrollCard($idd);
    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
} elseif ($command == 'enrollFinger') {
    $idd  = $_REQUEST['idd'];
    $temp_id = $_REQUEST['temp_id'];

    if (empty($idd)) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }

    $sql    = 'SELECT * FROM employee WHERE idd="' . $idd . '"';
    $result = $db->query($sql);
    if ($db->num_rows($result) <= 0) {
        echo json_encode(array(
            'success' => false,
        ));
        exit;
    }
    $row = $db->fetch_array($result);

    $data    = $anvizCommand->setEnrollFinger($idd, $temp_id, $row['fingersign']);
    $id      = $data['id'];
    $params  = $data['params'];
    $command = $data['command'];
    $content = $data['content'];

    $sql = 'INSERT INTO device_command(id, device_id, command, content, status, params)
            VALUES ("' . $id . '", "' . $device_id . '", "' . $command . '", "' . $content . '", 0, "' . base64_encode(json_encode($params)) . '")';
    $db->query($sql);

    echo json_encode(array(
        'success' => true,
        'data'    => array(
            'id' => $id,
        ),
    ));
}
