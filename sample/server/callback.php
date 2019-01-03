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
        global $db;

        $token = Tools::randomkey(8);
        $expirestime = time() + 24 * 60 * 60;

        $sql = 'SELECT * FROM device WHERE serial_number="' . $data['serial_number'] . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0) {
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
            $db->query($sql);
        } else {
            $row = $db->fetch_array($result);
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
            $db->query($sql);
        }

        return array(
            'id' => $id,
            'token' => $token
        );
    }

    public function getToken($id)
    {
        global $db;

        $sql = 'SELECT token, expirestime FROM device WHERE id="' . $id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $row = $db->fetch_array($result);
        if (empty($row['token']) || $row['expirestime'] < time())
            return false;

        return $row['token'];
    }

    public function login($id, $dusername = '', $dpassword = '')
    {
        global $db;

        $sql = 'SELECT id FROM users WHERE dusername="' . $dusername . '" AND dpassword="' . $dpassword . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $row = $db->fetch_array($result);
        $user_id = $row['id'];

        $sql = 'UPDATE device SET user_id="' . $user_id . '", is_login=1 WHERE id="' . $id . '"';
        $db->query($sql);

        return true;
    }

    public function record($id, $command_id, $data)
    {
        global $db;

        $sql = 'SELECT * FROM device WHERE id="' . $id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $device = $db->fetch_array($result);
        $device_id = $device['id'];
        $user_id = $device['user_id'];

        foreach ($data as $row) {
            $idd = $row['idd'];
            $checktime = $row['checktime'];

            $sql = 'SELECT * FROM records WHERE idd="' . $idd . '" AND device_id="' . $device_id . '" AND checktime="' . $checktime . '"';
            $result = $db->query($sql);
            if ($db->num_rows($result) > 0)
                continue;

            $sql = 'INSERT INTO records(idd, device_id, checktime, user_id) VALUES ("' . $idd . '", "' . $device_id . '", "' . $checktime . '", "' . $user_id . '")';
            $db->query($sql);
        }

        $sql = 'SELECT * FROM device_command WHERE id="' . $command_id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return true;

        $row = $db->fetch_array($result);
        $params = json_decode(base64_decode($row['params']), true);

        if (!$data || count($data) < $params['limit']) {
            $db->query('DELETE from device_command WHERE id="' . $command_id . '"');
        } else {
            $params['start'] += count($data);
            $anvizCommand = new AnvizCommand();
            $_data = $anvizCommand->getRecords($params['start'], $params['limit']);
            $content = $_data['content'];

            $db->query('UPDATE device_command SET content="' . $content . '", status=0, params="' . base64_encode(json_encode($params)) . '" WHERE id="' . $command_id . '"');
        }

        return true;
    }

    public function employee($id, $command_id, $data)
    {
        global $db;

        $sql = 'SELECT * FROM device WHERE id="' . $id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $device = $db->fetch_array($result);
        $device_id = $device['id'];
        $user_id = $device['user_id'];

        foreach ($data as $row) {
            $idd = $row['idd'];
            $name = $row['name'];
            $passd = $row['passd'];
            $cardid = $row['cardid'];
            $group_id = $row['group_id'];
            $fingersign = $row['fingersign'];
            $is_admin = $row['is_admin'];

            $sql = 'SELECT * FROM employee WHERE idd="' . $idd . '"';
            $result = $db->query($sql);
            if ($db->num_rows($result) > 0) {
                $sql = 'UPDATE employee SET passd="' . $passd . '", name="' . $name . '", cardid="' . $cardid . '", group_id=' . $group_id . ', fingersign=' . $fingersign . ', is_admin=' . $is_admin . ' WHERE idd="' . $idd . '"';
                $db->query($sql);
            } else {
                $sql = 'INSERT INTO employee(idd, passd, cardid, name, group_id, fingersign, is_admin) VALUES ("' . $idd . '", "' . $passd . '", "' . $cardid . '", "' . $name . '", ' . $group_id . ', ' . $fingersign . ', ' . $is_admin . ')';
                $db->query($sql);
            }
        }

        $sql = 'SELECT * FROM device_command WHERE id="' . $command_id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return true;

        $row = $db->fetch_array($result);
        $params = json_decode(base64_decode($row['params']), true);

        if (!$data || count($data) < $params['limit']) {
            $db->query('DELETE from device_command WHERE id="' . $command_id . '"');
        } else {
            $params['start'] += count($data);
            $anvizCommand = new AnvizCommand();
            $_data = $anvizCommand->getEmployees($params['start'], $params['limit']);
            $content = $_data['content'];

            $db->query('UPDATE device_command SET content="' . $content . '", status=0, params="' . base64_encode(json_encode($params)) . '"');
        }

        return true;
    }

    public function finger($id, $command_id, $data)
    {
        global $db;

        $sql = 'SELECT * FROM device WHERE id="' . $id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $device = $db->fetch_array($result);
        $device_id = $device['id'];
        $user_id = $device['user_id'];

        foreach ($data as $row) {
            $idd = $row['idd'];
            $sign = $row['sign'];
            $temp_id = $row['temp_id'];
            $template = base64_encode($row['template']);

            $sql = 'SELECT * FROM employee_template WHERE idd="' . $idd . '" AND sign=' . $sign . ' AND temp_id=' . $temp_id;
            $result = $db->query($sql);
            if ($db->num_rows($result) > 0) {
                $sql = 'UPDATE employee_template SET content="' . $template . '" WHERE idd="' . $idd . '" AND sign=' . $sign . ' AND temp_id=' . $temp_id;
                $db->query($sql);
            } else {
                $sql = 'INSERT INTO employee_template(idd, sign, temp_id, content) VALUES ("' . $idd . '", "' . $sign . '", "' . $temp_id . '", "' . $template . '")';
                $db->query($sql);
            }
        }

        $sql = 'SELECT * FROM device_command WHERE id="' . $command_id . '"';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return true;

        $row = $db->fetch_array($result);
        $params = json_decode(base64_decode($row['params']), true);

        if (!$data || count($data) < $params['limit']) {
            $db->query('DELETE from device_command WHERE id="' . $command_id . '"');
        } else {
            $params['start'] += count($data);
            $anvizCommand = new AnvizCommand();
            $_data = $anvizCommand->getFingers($params['start'], $params['limit']);
            $content = $_data['content'];

            $db->query('UPDATE device_command SET content="' . $content . '", status=0, params="' . base64_encode(json_encode($params)) . '"');
        }

        return true;
    }

    public function other($id, $command_id)
    {
        global $db;

        $db->query('DELETE from device_command WHERE id="' . $command_id . '"');

        return true;
    }

    public function updateLastlogin($id)
    {
        global $db;

        $sql = 'UPDATE device SET lasttime=' . time() . ',  is_login=1 WHERE id="' . $id . '"';
        $db->query($sql);
    }

    public function getNextCommand($id)
    {
        global $db;

        $sql = 'SELECT * FROM device_command WHERE device_id="' . $id . '" AND status=0 LIMIT 1';
        $result = $db->query($sql);
        if ($db->num_rows($result) <= 0)
            return false;

        $row = $db->fetch_array($result);

        $data = array(
            'id' => $row['id'],
            'command' => $row['command'],
            'content' => base64_decode($row['content'])
        );

        $sql = 'UPDATE device_command SET status=1 WHERE id="' . $row['id'] . '"';
        $db->query($sql);

        return $data;
    }
}