<?php

/**
 * File Name: Protocol.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-22
 * Time: 9:44
 * Description:
 */

/**
 * The communication instructions of device
 */
define('KEY', 'AnvizCloudForAttDevice');
define('CMD_LOGIN', 9001); //Login
define('CMD_NOCOMMAND', 9002); //Heartbeat
define('CMD_FORBIDDEN', 9003); //Disable connection
define('CMD_REGESTER', 9004); //Register
define('CMD_ERROR', 9005); //The command error
define('CMD_GETRECORDUSERFPCOUNT', 9007); //Get User & Record & FP number in device

define('CMD_GETNETWORK', 1003); //Get network parameters
define('CMD_SETDATETIME', 1004); //Config network parameters
define('CMD_SETADMINPASSWORD', 9009); //Modify the super admin password in device

define('CMD_GETALLEMPLOYEE', 2001); //Download all employees from device(Does not include fingerprint template and attendance records)
define('CMD_PUTALLEMPLOYEE', 2101); //Upload employee data in bulk
define('CMD_GETONEEMPLOYEE', 2002); //Download the specified employee information
define('CMD_PUTONEEMPLOYEE', 2102); //Upload the specified employee information
define('CMD_DELETEALLEMPLOYEE', 2021); //Clear all employees form deivce(And clear all fingerprint template, but not clear attendance records)
define('CMD_DELETEONEEMPLOYEE', 2022); //Delete the specified employee from device

define('CMD_GETALLFINGER', 2031); //Download all fingerprint templates from device
define('CMD_PUTALLFINGER', 2131); //Upload the fingerprint templates in bulk
define('CMD_GETONEFINGER', 2032); //download templates of the specified employee
define('CMD_PUTONEFINGER', 2132); //Upload template of the specified employee
define('CMD_DELETEALLFINGER', 2041); //Clear all fingerprint templates from device(But not clear employee and attendance records)
define('CMD_DELETEONEFINGER', 2042); //Delete templates of the specified from device
define('CMD_ENROLLFINGER', 2033); //Remote registration fingerprint
define('CMD_ENROLLCARD', 9008); //Remote registration card

define('CMD_GETALLRECORD', 3001); //Download all attendance records form device
define('CMD_GETNEWRECORD', 3002); //Download new attendance records from device

class Protocol
{
    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : explodeCommand
     *
     * @param $token
     * @param $data
     *
     * @return bool
     * @Description:
     */
    public static function explodeCommand($token, $data)
    {
        if (empty($token) || empty($data)) {
            return false;
        }

        $sha1 = substr(sha1(KEY . $token), 16, 8);

        $data = base64_decode($data);

        $data                = Tools::decrypt3DES($data, $sha1);
        $result["device_id"] = trim(substr($data, 0, 32));
        $result["id"]        = trim(substr($data, 32, 8));
        $result["command"]   = trim(substr($data, 40, 4));
        $result["length"]    = trim(substr($data, 48, 8));
        $result["content"]   = @str_pad(substr($data, 56), $result['length'], ' ', STR_PAD_RIGHT);

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : RegisterDevice
     *
     * @param string $data
     *
     * @return array|bool
     * @Description:
     */
    public static function RegisterDevice($data = '')
    {
        if (empty($data)) {
            return false;
        }

        $data = base64_decode($data);

        $result = array();
        /** Serial number */
        $result["serial_number"] = trim(substr($data, 0, 20));
        /** Deivce model */
        $result["model"] = trim(substr($data, 20, 20));
        /** Firmware */
        $result["firmware"] = trim(substr($data, 40, 20));
        /** Communication protocol version */
        $result["protocol"] = trim(substr($data, 60, 20));

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : LoginDevice
     *
     * @param string $content
     *
     * @return array|bool
     * @Description:
     */
    public static function LoginDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        $result = array();

        $result['username']  = trim(substr($content, 0, 20));
        $result['dpassword'] = trim(substr($content, 20, 20));

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : NetworkDevice
     *
     * @param string $content
     *
     * @return array|bool
     * @Description:
     */
    public static function NetworkDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        $result              = array();
        $result['internet']  = ord($content[0]);
        $result['ipaddress'] = ord($content[1]) . "." . ord($content[2]) . "." . ord($content[3]) . "." . ord($content[4]);
        $result['netmask']   = ord($content[5]) . "." . ord($content[6]) . "." . ord($content[7]) . "." . ord($content[8]);
        $result['mac']       = strtoupper(str_pad(sprintf("%x", ord($content[9])), 2, "0", STR_PAD_LEFT)) . '-'
        . strtoupper(str_pad(sprintf("%x", ord($content[10])), 2, "0", STR_PAD_LEFT)) . '-'
        . strtoupper(str_pad(sprintf("%x", ord($content[11])), 2, "0", STR_PAD_LEFT)) . '-'
        . strtoupper(str_pad(sprintf("%x", ord($content[12])), 2, "0", STR_PAD_LEFT)) . '-'
        . strtoupper(str_pad(sprintf("%x", ord($content[13])), 2, "0", STR_PAD_LEFT)) . '-'
        . strtoupper(str_pad(sprintf("%x", ord($content[14])), 2, "0", STR_PAD_LEFT));
        $result['gateway'] = ord($content[15]) . "." . ord($content[16]) . "." . ord($content[17]) . "." . ord($content[18]);
        /*$result['serverip'] = ord($content[19]) . "." . ord($content[20]) . "." . ord($content[21]) . "." . ord($content[22]);
        $result['remote'] = ord($content[23]);
        $result['port'] = (ord($content[24]) << 8) + ord($content[25]);
        $result['comm_method'] = ord($content[26]);*/
        $result['dhcp'] = ord($content[27]);

        return $result;
    }

    public static function RecordUserFPCountDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        $result           = array();
        $result['record'] = substr($content, 0, 8);
        $result['user']   = substr($content, 8, 8);
        $result['fp']     = substr($content, 16, 8);

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : EmployeeDevice
     *
     * @param string $content
     *
     * @return array|bool
     * @Description:
     */
    public static function EmployeeDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        /**
         * The length of each employee information is 40
         * if the length of data can not be 40 whole, it's dirty data
         */

        if (strlen($content) % 40 != 0) {
            return false;
        }

        $result = array();

        /** the total of employee in this acquisition */
        $count = strlen($content) / 40;
        for ($i = 0; $i < $count; $i++) {
            $row = substr($content, $i * 40, 40);

            $record = array();
            /** ID On Device */
            $record['idd'] = (ord($row[0]) << 32) + (ord($row[1]) << 24) + (ord($row[2]) << 16) + (ord($row[3]) << 8) + ord($row[4]);

            if (ord($row[5]) == 0xFF and ord($row[6]) == 0xFF and ord($row[7]) == 0xFF) {
                $record['passd'] = '';
            } else {
                /** The length of password */
                $passlen = intval(ord($row[5]) >> 4);
                /** Attendance Password */
                $record['passd'] = ((ord($row[5]) & 0x0F) << 16) + (ord($row[6]) << 8) + ord($row[7]);
                $record['passd'] = str_pad($record['passd'], $passlen, '0', STR_PAD_LEFT);
            }

            /** Card number */
            if (ord($row[8]) == 0xFF and ord($row[9]) == 0xFF and ord($row[10]) == 0xFF and ord($row[11]) == 0xFF) {
                $record['cardid'] = '';
            } else {
                $record['cardid'] = (ord($row[8]) << 24) + (ord($row[9]) << 16) + (ord($row[10]) << 8) + ord($row[11]);
            }

            /** Last Name */
            $record['name'] = '';
            for ($_i = 0; $_i < 10; $_i++) {
                $temp = (ord($row[$_i * 2 + 13]) << 8) + ord($row[$_i * 2 + 12]);
                if (empty($temp)) {
                    continue;
                }
                $record['name'] .= Tools::uni2utf8($temp);
            }
            $record['name'] = empty($record['name']) ? $record['idd'] : $record['name'];

            /** Department ID */
            //$record['deptid'] = ord($row[32]);

            /** Group ID */
            $record['group_id'] = ord($row[33]);

            /** The sign of the finger had been register */
            $record['fingersign'] = (ord($row[35]) << 8) + ord($row[36]);

            /** Whether administrator */
            $record['is_admin'] = ord($row[37]);

            $result[$i] = $record;
        }

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : FingerDevice
     *
     * @param string $content
     *
     * @return array|bool
     * @Description:
     */
    public static function FingerDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        /**
         * The length of each finger information is 344
         * if the length of data can not be 344 whole, it's dirty data
         */
        if (strlen($content) % 344 != 0) {
            return false;
        }
        $result = array();

        /** the total of finger in this acquisition */
        $count = strlen($content) / 344;
        for ($i = 0; $i < $count; $i++) {
            $row = substr($content, $i * 344, 344);

            $record = array();

            /** ID On Device */
            $record['idd'] = (ord($row[0]) << 32) + (ord($row[1]) << 24) + (ord($row[2]) << 16) + (ord($row[3]) << 8) + ord($row[4]);
            /**
             * 1: Fingerprint
             * 2: Facepass
             */
            $record['sign'] = 1;
            /** The number of finger */
            $record['temp_id'] = ord($row[5]);
            /** the data of finger */
            $record['template'] = substr($row, 6, 338);

            $result[$i] = $record;
        }

        return $result;
    }

    public static function EnrollFinger($content = '')
    {
        if (empty($content)) {
            return false;
        }

        if (strlen($content) % 344 != 0) {
            return false;
        }
        $result = array();
        $row    = substr($content, 0, 344);

        /** ID On Device */
        $result['idd'] = (ord($row[0]) << 32) + (ord($row[1]) << 24) + (ord($row[2]) << 16) + (ord($row[3]) << 8) + ord($row[4]);
        /**
         * 1: Fingerprint
         * 2: Facepass
         */
        $result['sign'] = 1;
        /** The number of finger */
        $result['temp_id'] = ord($row[5]);
        /** the data of finger */
        $result['template'] = substr($row, 6, 338);

        return $result;
    }

    public static function EnrollCardDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        $result = array();
        /** ID On Device */
        $result['idd'] = (ord($content[0]) << 32) + (ord($content[1]) << 24) + (ord($content[2]) << 16) + (ord($content[3]) << 8) + ord($content[4]);

        if (ord($content[5]) == 0xFF and ord($content[6]) == 0xFF and ord($content[7]) == 0xFF and ord($content[8]) == 0xFF) {
            $result['cardid'] = '';
        } else {
            $result['cardid'] = (ord($content[5]) << 24) + (ord($content[6]) << 16) + (ord($content[7]) << 8) + ord($content[8]);
        }

        return $result;
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : RecordDevice
     *
     * @param string $content
     *
     * @return array|bool
     * @Description:
     */
    public static function RecordDevice($content = '')
    {
        if (empty($content)) {
            return false;
        }

        /**
         * The length of each record is 16
         * if the length of data can not be 16 whole, it's dirty data
         */
        if (strlen($content) % 16 != 0) {
            return false;
        }
        $result = array();

        /** the total of records in this acquisition */
        $count = strlen($content) / 16;
        for ($i = 0; $i < $count; $i++) {
            $row = substr($content, $i * 16, 16);

            $record = array();

            /** ID On Device */
            $record['idd'] = (ord($row[0]) << 32) + (ord($row[1]) << 24) + (ord($row[2]) << 16) + (ord($row[3]) << 8) + ord($row[4]);
            /** Check Time */
            $record['checktime'] = (ord($row[5]) << 24) + (ord($row[6]) << 16) + (ord($row[7]) << 8) + ord($row[8]);
            $record['checktime'] = $record['checktime'] + strtotime('2000-01-02 00:00:00');
            /** Check Type */
            //$record['checktype'] = ord($row[9]);
            /** Work Type */
            //$record['worktype'] = ord($row[10]);

            $result[$i] = $record;
        }

        return $result;
    }

    public static function RecordImport($content = '')
    {
        if (empty($content)) {
            return false;
        }

        /**
         * The length of each record is 16
         * if the length of data can not be 16 whole, it's dirty data
         */
        if ((strlen($content) - 3) % 14 != 0) {
            return false;
        }
        $result = array();

        $count = (ord($content[0]) << 16) + (ord($content[1]) << 8) + ord($content[2]);
        for ($i = 0; $i < $count; $i++) {
            $row = substr($content, $i * 14 + 3, 14);

            $record = array();

            /** ID On Device */
            $record['idd'] = (ord($row[0]) << 32) + (ord($row[1]) << 24) + (ord($row[2]) << 16) + (ord($row[3]) << 8) + ord($row[4]);
            /** Check Time */
            $record['checktime'] = (ord($row[5]) << 24) + (ord($row[6]) << 16) + (ord($row[7]) << 8) + ord($row[8]);
            $record['checktime'] = $record['checktime'] + strtotime('2000-01-02 00:00:00');

            $result[$i] = $record;
        }

        return $result;
    }

    public static function setDeviceDateTime($data = array())
    {
        if (empty($data)) {
            return false;
        }

        $year   = empty($data['year']) ? date('Y') : $data['year'];
        $month  = empty($data['month']) ? date('m') : $data['month'];
        $day    = empty($data['day']) ? date('d') : $data['day'];
        $hour   = empty($data['hour']) ? 0 : $data['hour'];
        $minute = empty($data['minute']) ? 0 : $data['minute'];
        $second = empty($data['second']) ? 0 : $data['second'];

        if ($year >= 2000) {
            $year = $year - 2000;
        }

        $pack = '';

        $pack .= pack('C', $year);
        $pack .= pack('C', $month);
        $pack .= pack('C', $day);
        $pack .= pack('C', $hour);
        $pack .= pack('C', $minute);
        $pack .= pack('C', $second);

        return $pack;
    }

    public static function getAllEmployee($data = array())
    {
        $start = empty($data['start']) ? 0 : $data['start'];
        $limit = empty($data['limit']) ? 100 : $data['limit'];

        $pack = '';
        $pack .= str_pad($start, 8, '0', STR_PAD_LEFT);
        $pack .= str_pad($limit, 8, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function getEmployee($idd)
    {
        $pack = '';
        $pack .= str_pad($idd, 16, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function setEmployee($employee)
    {
        if (empty($employee)) {
            return false;
        }

        if (!isset($employee['idd'])) {
            return false;
        }

        $idd           = $employee['idd'];
        $passd         = isset($employee['passd']) ? $employee['passd'] : '';
        $cardid        = isset($employee['cardid']) ? $employee['cardid'] : '';
        $name          = isset($employee['name']) ? $employee['name'] : $idd;
        $deptid        = isset($employee['deptid']) ? $employee['deptid'] : 0;
        $is_admin      = isset($employee['is_admin']) ? $employee['is_admin'] : 64;
        $group_id      = isset($employee['group_id']) ? $employee['group_id'] : 0;
        $identity_type = isset($employee['identity_type']) ? $employee['identity_type'] : 6;
        $fingersign    = 0;

        $pack = '';

        //IDD
        $pack = pack("C", intval($idd / 0x00FFFFFFFF)) . pack("N", $idd & 0x00FFFFFFFF);

        //密码：5~7
        if (empty($passd)) {
            $pack .= substr(pack('N', 0xFF), 0, 3);
        } else {
            $length = strlen($passd) << 4;
            $length = intval($length) + intval($passd >> 16);
            $pack .= substr(pack('n', $length), 1, 1) . substr(pack('N', $passd), 2, 2);
        }

        //Cardid
        if (empty($cardid)) {
            $pack .= pack('N', 0xFFFFFFFF);
        } else {
            $pack .= pack('N', $cardid);
        }

        //Name
        if (strlen($name) > 10) {
            $name = substr($name, 0, 10);
        }
        $pack .= str_pad(Tools::utf82uni($name), 20, pack('v', 0x00), STR_PAD_RIGHT);

        //Department ID
        $pack .= pack('C', $deptid);

        //Group ID
        $pack .= pack('C', $group_id);

        //
        $pack .= pack('C', $identity_type);

        //Finger sign
        $pack .= pack('n', $fingersign);

        //Is admin
        $pack .= pack('C', $is_admin);

        //
        $pack .= pack('C', 0x20);
        //
        $pack .= pack('C', 0x20);

        return $pack;
    }

    public static function delEmployee($idd)
    {
        if (empty($idd)) {
            return false;
        }

        $pack = '';

        //IDD
        $pack = pack("C", intval($idd / 0x00FFFFFFFF)) . pack("N", $idd & 0x00FFFFFFFF);

        return $pack;
    }

    public static function getAllFinger($data = array())
    {
        $start = empty($data['start']) ? 0 : $data['start'];
        $limit = empty($data['limit']) ? 100 : $data['limit'];

        $pack = '';
        $pack .= str_pad($start, 8, '0', STR_PAD_LEFT);
        $pack .= str_pad($limit, 8, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function getFinger($idd)
    {
        if (empty($idd)) {
            return false;
        }

        $pack = pack("C", intval($idd / 0x00FFFFFFFF)) . pack("N", $idd & 0x00FFFFFFFF);

        return $pack;

    }

    public static function setFinger($finger)
    {
        if (empty($finger) || empty($finger['idd']) || empty($finger['template'])) {
            return false;
        }

        $idd  = $finger['idd'];
        $sign = empty($finger['sign']) ? 0 : $finger['sign'];
        $fp   = $finger['template'];

        $pack = '';
        $pack .= pack("C", intval($idd / 0x00FFFFFFFF)) . pack("N", $idd & 0x00FFFFFFFF);
        $pack .= pack('C', $sign);
        $pack .= $fp;

        return $pack;
    }

    public static function setEnrollFinger($finger)
    {
        if (empty($finger) || empty($finger['idd'])) {
            return false;
        }

        $idd    = $finger['idd'];
        $sign   = empty($finger['sign']) ? 0 : $finger['sign'];
        $signed = empty($finger['signed']) ? 0 : $finger['signed'];

        $pack = '';
        $pack .= str_pad($idd, 8, '0', STR_PAD_LEFT);
        $pack .= str_pad($sign, 8, '0', STR_PAD_LEFT);
        $pack .= pack('n', $signed);

        return $pack;
    }


    public static function setEnrollCard($idd)
    {
        if (empty($idd)) {
            return false;
        }

        $pack = '';
        $pack .= str_pad($idd, 8, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function deleteFinger($finger)
    {
        if (empty($finger) || empty($finger['idd'])) {
            return false;
        }

        $idd  = $finger['idd'];
        $sign = empty($finger['sign']) ? 0 : $finger['sign'];

        $pack = '';
        $pack .= pack("C", intval($idd / 0x00FFFFFFFF)) . pack("N", $idd & 0x00FFFFFFFF);
        $pack .= str_pad($sign, 5, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function getAllRecord($data)
    {
        $start = empty($data['start']) ? 0 : $data['start'];
        $limit = empty($data['limit']) ? 100 : $data['limit'];

        $pack = '';
        $pack .= str_pad($start, 8, '0', STR_PAD_LEFT);
        $pack .= str_pad($limit, 8, '0', STR_PAD_LEFT);

        return $pack;
    }

    public static function setSuperAdminPassword($password)
    {
        if (empty($password)) {
            return false;
        }
        $password = substr($password, 0, 6);
        $password = str_pad($password, 6, 0, STR_PAD_LEFT);

        $pack = '';
        $pack .= str_pad($password, 8, 0, STR_PAD_RIGHT);

        return $pack;
    }

    public static function showRegister($device_id)
    {
        return Tools::R(self::joinCommand('11111111', $device_id, '11111111', CMD_REGESTER, 1, ''));
    }

    public static function showError($sha1, $device_uuid, $command = '')
    {
        return Tools::R(self::joinCommand($sha1, $device_uuid, '11111111', CMD_ERROR, 5, $command));
    }

    public static function showForbidden()
    {
        return Tools::R(self::joinCommand('11111111', '22222222', '22222222', CMD_FORBIDDEN, 5, ''));
    }

    public static function showNocommand($token, $device_id)
    {
        return Protocol::joinCommand($token, $device_id, '11111111', CMD_NOCOMMAND, 5);
    }

    /**
     * @Created    by Jacobs <jacobs@anviz.com>
     * @Name       : joinCommand
     *
     * @param        $sha1         Token value
     * @param        $device_id
     * @param        $id
     * @param        $command
     * @param        $nexttime
     * @param int    $length
     * @param string $content
     *
     * @return bool|string
     * @Description:
     */
    public static function joinCommand($token, $device_id, $id, $command, $nexttime, $content = "")
    {

        if (empty($token) || empty($device_id) || empty($id) || empty($command)) {
            return false;
        }

        $sha1 = substr(sha1(KEY . $token), 16, 8);

        $id = empty($id) ? '11111111' : str_pad($id, 8, ' ', STR_PAD_LEFT);

        $command = empty($command) ? '0000' : str_pad($command, 4, ' ', STR_PAD_LEFT);
        /** Next heartbeat packet send interval time */
        /** eg.（0，5，10，60，300）*/
        $nextime = empty($command) ? '0005' : str_pad($nexttime, 4, 0, STR_PAD_LEFT);

        $length = strlen($content);
        $length = str_pad($length, 8, 0x00, STR_PAD_LEFT);

        $device_id = str_pad($device_id, 32, 0x00, STR_PAD_LEFT);

        $string = $device_id . $id . $command . $nextime . $length . $content;

        switch ($command) {
            case CMD_REGESTER:
            case CMD_FORBIDDEN:
                return $string;

            default:
                return Tools::encrypt3DES($string, $sha1);
        }
    }
}
