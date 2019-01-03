<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午2:13
 * File Name: ajax.php
 */
require(dirname(__FILE__) . '/../config.php');
require(dirname(__FILE__) . '/../database.php');

$method = $_REQUEST['method'] ? $_REQUEST['method'] : '';

if ($method == 'command_status') {
    $id = $_REQUEST['id'];
    if (!empty($id)) {
        $sql = 'SELECT * FROM device_command WHERE id="' . $id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    }
} else {
    $sql = 'SELECT * from device';
    $result = $db->query($sql);
    $device = array();
    if ($db->num_rows($result) > 0) {
        while ($row = $db->fetch_array($result)) {
            $id = $row['id'];
            $token = $row['token'];
            $lasttime = $row['lasttime'];
            $is_login = $row['is_login'];
            if ($lasttime <= time() - 30) {
                $sql = 'UPDATE device SET is_login=0 WHERE id="' . $id . '"';
                $db->query($sql);
                $is_login = 0;
            }

            $device[$id] = array(
                'id' => $id,
                'token' => $token,
                'lasttime' => date('m/d/Y H:i:s', $lasttime),
                'is_login' => $is_login
            );
        }
    }

    echo json_encode(array(
        'device' => $device
    ));
}