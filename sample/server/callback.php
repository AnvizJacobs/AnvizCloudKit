<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 下午4:51
 * File Name: callback.php
 */

class callback implements AnvizInterface
{
    public function register($data)
    {
        $token = Tools::randomkey(8);
        $expirestime = time() + 24 * 60 * 60;

        $sql = 'SELECT * FROM device WHERE serial_number="' . $data['serial_number'] . '"';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0) {
            $id = Tools::uuid();
            $sql = 'INSERT INTO device(id, serial_number, model, firmware, protocol, token, expirestime, createdtime) VALUES (
              "' . $id . '",
              "' . $data['serial_number'] . '",
              "' . $data['model'] . '",
              "' . $data['firmware'] . '",
              "' . $data['protocol'] . '",
              ' . $token . ',
              ' . $expirestime . ',
              ' . time() . '
            )';
            mysql_query($sql);
        } else {
            $row = mysql_fetch_array($result);
            $id = $row['id'];
            $sql = 'UPDATE device SET 
              model="' . $data['model'] . '",
              firmware="' . $data['firmware'] . '",
              protocol="' . $data['protocol'] . '",
              is_login=0,
            ';
            if ($row['expirestime'] <= time()) {
                $sql .= ', token="' . $token . '", expirestime=' . $expirestime;
            } else {
                $token = $row['token'];
            }
            mysql_query($sql);
        }

        return array(
            'id' => $id,
            'token' => $token
        );
    }

    public function getToken($id)
    {
        $sql = 'SELECT token, expirestime FROM device WHERE id="' . $id . '"';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0)
            return false;

        $row = mysql_fetch_array($result);
        if (empty($row['token']) || $row['expirestime'] < time())
            return false;

        return $row['token'];
    }

    public function login($id, $dusername = '', $dpassword = '')
    {
        $sql = 'SELECT id FROM users WHERE dusername="' . $dusername . '" AND dpassword="' . $dpassword . '"';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0)
            return false;

        $row = mysql_fetch_array($result);
        $user_id = $row['id'];

        $sql = 'UPDATE device SET user_id="' . $user_id . '", is_login=1 WHERE id="' . $id . '"';
        mysql_query($sql);

        return true;
    }

    public function record($id, $command_id, $data)
    {
        $sql = 'SELECT * FROM device WHERE id="' . $id . '"';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0)
            return false;

        $device = mysql_fetch_array($result);
        $device_id = $device['id'];
        $user_id = $device['user_id'];

        foreach ($data as $row) {
            $idd = $row['idd'];
            $checktime = $row['checktime'];

            $sql = 'SELECT * FROM records WHERE idd="' . $idd . '" AND device_id="' . $device_id . '" AND checktime="' . $checktime . '"';
            $result = mysql_query($sql);
            if (mysql_num_rows($result) > 0)
                continue;

            $sql = 'INSERT INTO records(idd, device_id, checktime, user_id) VALUES ("' . $idd . '", "' . $device_id . '", "' . $checktime . '", "' . $user_id . '")';
            mysql_query($sql);
        }

        $sql = 'SELECT * FROM device_command WHERE id="' . $command_id . '"';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0)
            return true;

        $row = mysql_fetch_array($result);
        $params = json_decode(base64_decode($row['params']), true);

        if (!$data || count($data) < $params['limit']) {
            mysql_query('DELETE from device_command WHERE id="' . $command_id . '"');
        } else {
            $params['start'] += count($data);
            $content = Protocol::getAllRecord($params);
            mysql_query('UPDATE device_command SET content="' . $content . '", status=0, params="' . base64_encode(json_encode($params)) . '"');
        }
    }

    public function updateLastlogin($id)
    {
        $sql = 'UPDATE device SET lasttime=' . time() . ',  is_login=1 WHERE id="' . $id . '"';
        mysql_query($sql);
    }

    public function getNextCommand($id)
    {
        $sql = 'SELECT * FROM device_command WHERE device_id="' . $id . '" AND status=0 LIMIT 1';
        $result = mysql_query($sql);
        if (mysql_num_rows($result) <= 0)
            return false;

        $row = mysql_fetch_array($result);

        $data = array(
            'id' => $row['id'],
            'command' => $row['command'],
            'content' => $row['content']
        );

        $sql = 'UPDATE device_command SET status=1 WHERE id="' . $row['id'] . '"';
        mysql_query($sql);

        return $data;
    }
}