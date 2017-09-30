<?php

/**
 * Created by Jacobs.
 * Auth: jacobs@anviz.com
 * Copyright: Anviz Global Inc.
 * Date: 2017/9/19
 * Time: 11:08
 * FileName: interface.php
 */
require_once(dirname(__FILE__) . '/lib/Protocol.php');
require_once(dirname(__FILE__) . '/lib/Tools.php');

/**
 * Interface AnvizInterface
 * The callback class for the device response server instruction
 */
interface AnvizInterface
{
    /**
     * @author jacobs@anviz.com
     * @param $device
     * @return mixed
     * @description Create a unique device ID, a string of no more than 32 characters
     * @params
     *      $device = array(
     *          'serial_number' => '1234567890123456',
     *          'model' => 'C2 Pro',
     *          'firmware' => '02.12.38',
     *          'protocol' => 'V1.0'
     *      );
     *      serial_number: int Serial number
     *      model: string Device model
     *      firmware: string Firmware version
     *      protocol: string Protocol version
     * @return string Device ID
     */
    public function createId($device);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @return mixed
     * @description Generates a Token value for communication data encryption. The token must be save binding with the device ID.
     * @params
     *      device_id: string Devicd ID
     * @return int Token no more than 8 digits
     */
    public function createToken($device_id);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @return mixed
     * @description Query token by device ID
     * @params
     *      device_id: int Device ID
     * @return int Token no more than 8 digits
     */
    public function getToken($device_id);

    /**
     * @author jacobs@anviz.com
     * @param $token
     * @param $device_id
     * @return mixed
     * @description Verify the token
     * @params:
     *      token: int Token
     *      device_id: string Device ID
     * @return boolean
     *      true: Active
     *      false: Inactive
     */
    public function validToken($token, $device_id);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $username
     * @param $password
     * @return mixed
     * @description Login authentication
     * @params
     *      device_id: string Device ID
     *      task_id: Task ID
     *      username: string username
     *      password: string password
     * @return boolean
     *      true: Verification is successful
     *      false: Verification is failed
     */
    public function Login($device_id, $task_id, $username, $password);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $network
     * @return mixed
     * @description Get device network information
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *      network:
     *             array(
     *                  'internet' => 0,                    //0: Lan    1: WIFI
     *                  'ipaddress' => '192.168.1.100',
     *                  'netmask' => '255.255.255.0',
     *                  'mac' => '00:00:00:00:00:00',
     *                  'gateway' => '192.168.1.1',
     *                  'dhcp' => 0         //0: DHCP Off   1: DHCP On
     *             );
     * @return boolean
     */
    public function network($device_id, $task_id, $network);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $employees
     * @return mixed
     * @description Get employee information. By getEmployees/getEmployee will call back this method
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *      employees: array
     *              array(
     *                  array(
     *                      'idd' => 100,           //Employee ID on device
     *                      'passd' => '123456',    //Attendance password
     *                      'cardid' => '',         //Card Nunmber
     *                      'name' => 'Jacobs',     //Employee name
     *                      'group_id' => 1,        //Access control group
     *                      'fingersign' => 100,     //指纹标识，转换成二进制 0001100100 从低到高位分别代表第1~10根手指，该位为1表示已登记指纹
     *                      'is_admin' => 0         //Is device administrator. 0: user  1: admin
     *                  ),
     *                  array(
     *                      'idd' => 101,
     *                      'passd' => '123456',
     *                      'cardid' => '',
     *                      'name' => 'Jacobs',
     *                      'group_id' => 1,
     *                      'fingersign' => 10,
     *                      'is_admin' => 0
     *                  )
     *              )
     * @return boolean
     */
    public function employee($device_id, $task_id, $employees);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $fingers
     * @return mixed
     * @description Get fingerprint template. By getFingers/getFinger will call back this method
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *      fingers: array
     *            array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'sign' => 3,            //Finger sign, from 0~9 on behalf of the 1~10 finger
     *                      'template' => (BLOB)    //Fingerprint template. The field type select blob type when save to database
     *                  ),
     *                  array(
     *                      'idd' => 1001,
     *                      'sign' => 4,
     *                      'template' => (BLOB)
     *                  );
     *            );
     * @return boolean
     */
    public function finger($device_id, $task_id, $fingers);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $finger
     * @return mixed
     * @description Remote enroll fingerprint. By setEnrollFinger will call back this method
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *      fingers: array
     *            array(
     *                  'idd' => 1001,          //Employee ID on device
     *                  'sign' => 3,            //Finger sign, from 0~9 on behalf of the 1~10 finger
     *                  'template' => (BLOB)    //Fingerprint template. The field type select blob type when save to database
     *            );
     * @return boolean
     */
    public function enrollFinger($device_id, $task_id, $finger);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $records
     * @return mixed
     * @description Get records. By getRecords/getNewRecords will call back this method
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *      records: array
     *              array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'checktime' => 1356879141,  //Attendance time. Unix timestamp
     *                  ),
     *                  array(
     *                      'idd' => 1001,
     *                      'checktime' => 1356879141,
     *                  ),
     *              )
     * @return boolean
     */
    public function record($device_id, $task_id, $records);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @param $records
     * @return mixed
     * @description Get the callback function for real-time recording
     * @params
     *      device_id: string Device ID
     *      task_id: TASK ID
     *              array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'checktime' => 1356879141,  //Attendance time. Unix timestamp
     *                  ),
     *                  array(
     *                      'idd' => 1001,
     *                      'checktime' => 1356879141,
     *                  ),
     *              )
     * @return boolean
     */
    public function realRecord($device_id, $records);

    /**
     * @author jacobs@anviz.com
     * @param $device_id
     * @return mixed
     * @description Each time the device communicates with the server, this method is called to return the instructions that the device needs to execute.
     *              If return null, the device will execute next heartbeat after 5seconds.
     *              Instructions in the package method see the following AnvizCommand class method
     * @params
     *      device_id: string Device ID
     * @return NULL or String
     */
    public function getNextCommand($device_id);
}

/**
 * Class AnvizCommand
 * The construction method of the device command word
 */
class AnvizCommand
{
    public $device_id;      //Device ID
    public $token;          //Token
    public $task_id;        //Task ID no more than 8 digits

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of get network information
     */
    public function getNetwork()
    {
        if (empty($this->device_id) || empty($this->token))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETNETWORK, 0);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $timestamp
     * @return bool|string
     * @description The command of configure device datetime
     * @params
     *      timestamp: Unix timestamp
     */
    public function setDatetime($timestamp = 0)
    {
        $timestamp = empty($timestamp) ? time() : $timestamp;

        $data = array(
            'year' => date('Y', $timestamp),
            'month' => date('m', $timestamp),
            'day' => date('d', $timestamp),
            'hour' => date('H', $timestamp),
            'minute' => date('i', $timestamp),
            'second' => date('s', $timestamp)
        );
        $content = Protocol::setDeviceDateTime($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_SETDATETIME, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $start
     * @param int $limit
     * @return bool|string
     * @description The command of get multiple employee
     * @params
     *      start：position offset
     *      limit: the count of employee this time. no more then 100
     */
    public function getEmployees($start = 0, $limit = 100)
    {
        $start = empty($start) ? 0 : $start;
        $limit = empty($limit) ? 100 : $limit;

        $data = array(
            'start' => $start,
            'limit' => $limit
        );
        $content = Protocol::getAllEmployee($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETALLEMPLOYEE, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $idd
     * @return bool|string
     * @description The command of get the specified employee
     * @params
     *      idd: Employee ID on device
     */
    public function getEmployee($idd = 0)
    {
        if (empty($idd))
            return false;

        $content = Protocol::getEmployee($idd);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETONEEMPLOYEE, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $employees
     * @return bool|string
     * @description The command of bulk upload employee
     * @params
     *      employees: array
     *              array(
     *                  array(
     *                      'idd' => 100,           //Employee ID on device
     *                      'passd' => '123456',    //Attendance password
     *                      'cardid' => '',         //Card Number
     *                      'name' => 'Jacobs',     //Employee Name
     *                      'group_id' => 1,        //Access control group ID
     *                      'is_admin' => 0         //Is admin. 0 is user, 1 is admin
     *                  ),
     *                  array(
     *                      'idd' => 101,
     *                      'passd' => '123456',
     *                      'cardid' => '',
     *                      'name' => 'Jacobs',
     *                      'group_id' => 1,
     *                      'is_admin' => 0
     *                  )
     *              )
     */
    public function setEmployees($employees)
    {
        if (empty($employees))
            return false;

        $content = '';
        foreach ($employees as $employee) {
            $_content = Protocol::setEmployee($employee);
            if (empty($_content))
                return false;
            $content .= $_content;
        }

        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_PUTALLEMPLOYEE, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $employee
     * @return bool|string
     * @description The command of upload one employee
     * @params
     *      employee:
     *               array(
     *                      'idd' => 100,           //Employee ID on device
     *                      'passd' => '123456',    //Attendance password
     *                      'cardid' => '',         //Card Number
     *                      'name' => 'Jacobs',     //Employee Name
     *                      'group_id' => 1,        //Access control group ID
     *                      'is_admin' => 0         //Is admin. 0 is user, 1 is admin
     *                  ),
     */
    public function setEmployee($employee)
    {
        if (empty($employee))
            return false;

        $content = Protocol::setEmployee($employee);

        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_PUTONEEMPLOYEE, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of clear all employee (And clear all fingerprints, but not clear attendance records)
     */
    public function clearEmployee()
    {
        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_DELETEALLEMPLOYEE, 0);
    }

    /**
     * @author jacobs@anviz.com
     * @param $idds
     * @return bool|string
     * @description The command of bulk delete employees (And delete the fingerprints, but not delete attendance records)
     * @params
     *      idds: array
     *          array(1001, 1002, 1003） //Employee ID on device
     */
    public function deleteEmployee($idds)
    {
        if (empty($idds))
            return false;

        $content = '';
        foreach ($idds as $idd) {
            $_content = Protocol::delEmployee($idd);
            if (empty($_content))
                return false;

            $content .= $_content;
        }

        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_DELETEONEEMPLOYEE, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $start
     * @param int $limit
     * @return bool|string
     * @description The command of get all fingerprints
     * @params
     *      start：position offset
     *      limit: the count of employee this time. no more then 100
     */
    public function getFingers($start = 0, $limit = 10)
    {
        $start = empty($start) ? 0 : $start;
        $limit = empty($limit) ? 100 : $limit;

        $data = array(
            'start' => $start,
            'limit' => $limit
        );
        $content = Protocol::getAllFinger($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETALLFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $idd
     * @return bool|string
     * @description The command of download the specify employee fingerprint
     * @params
     *      idd：Employee ID on device
     */
    public function getFinger($idd)
    {
        if (empty($idd))
            return false;

        $content = Protocol::getFinger($idd);

        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETONEFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $fingers
     * @return bool|string
     * @description The command of bulk upload fingerprints
     * @params
     *      fingers: array  员工指纹信息
     *            array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'sign' => 3,            //Finger sign, from 0~9 on behalf of the 1~10 finger
     *                      'template' => (BLOB)    //Fingerprint template. The field type select blob type when save to database
     *                  ),
     *                  array(
     *                      'idd' => 1001,
     *                      'sign' => 4,
     *                      'template' => (BLOB)
     *                  );
     *            );
     *
     */
    public function setFingers($fingers)
    {
        if (empty($fingers))
            return false;

        $content = '';
        foreach ($fingers as $finger) {
            $_content = Protocol::setFinger($finger);
            if (empty($_content))
                return false;

            $content .= $_content;
        }

        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_PUTALLFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $finger
     * @return bool|string
     * @description The command of upload one fingerprint
     * @params
     *      finger: 指纹信息
     *           array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'sign' => 3,            //Finger sign, from 0~9 on behalf of the 1~10 finger
     *                      'template' => (BLOB)    //Fingerprint template. The field type select blob type when save to database
     *                  ),
     */
    public function setFinger($finger)
    {
        if (empty($finger))
            return false;

        $content = Protocol::setFinger($finger);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_PUTONEFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param $idd
     * @param int $sign
     * @param int $signed
     * @return bool|string
     * @description The command of remote enroll fingerprint
     * @params
     *      idd: Employee ID on device
     *      sign：Finger sign, from 0~9 on behalf of the 1~10 finger
     *      signed: 已登记的手指标识 转换成二进制 0001100100 从低到高位分别代表第1~10根手指，该位为1表示已登记指纹
     */
    public function setEnrollFinger($idd, $sign = 0, $signed = 0)
    {
        if (empty($idd))
            return false;
        $sign = empty($sign) ? 0 : $sign;
        $signed = empty($signed) ? 0 : $signed;
        $data = array(
            'idd' => $idd,
            'sign' => $sign,
            'signed' => $signed
        );
        $content = Protocol::setEnrollFinger($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_ENROLLFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of clear all fingerprint (but not clear employee and attendance record)
     */
    public function clearFinger()
    {
        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_DELETEALLFINGER, 0);
    }

    /**
     * @author jacobs@anviz.com
     * @param $idd
     * @param int $sign
     * @return bool|string
     * @description The command of delete the specify employee fingerprint(But not delete employee and attendance record)
     * @params
     *      idd: Employee ID on device
     *      sign: Finger sign, from 0~9 on behalf of the 1~10 finger
     */
    public function deleteFinger($idd, $sign = 0)
    {
        if (empty($idd))
            return false;

        $sign = empty($sign) ? 0 : $sign;
        $data = array(
            'idd' => $idd,
            'sign' => $sign
        );
        $content = Protocol::deleteFinger($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_DELETEONEFINGER, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $start
     * @param int $limit
     * @return bool|string
     * @description 获取所有记录信息
     * @params
     *      start：位置偏移量
     *      limit: 指纹数量
     */
    public function getRecords($start = 0, $limit = 100)
    {
        $start = empty($start) ? 0 : $start;
        $limit = empty($limit) ? 100 : $limit;

        $data = array(
            'start' => $start,
            'limit' => $limit
        );
        $content = Protocol::getAllRecord($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETALLRECORD, 0, $content);
    }

    /**
     * @author jacobs@anviz.com
     * @param int $start
     * @param int $limit
     * @return bool|string
     * @description 获取新记录信息
     * @params
     *      start：位置偏移量
     *      limit: 指纹数量
     */
    public function getNewRecords($start = 0, $limit = 100)
    {
        $start = empty($start) ? 0 : $start;
        $limit = empty($limit) ? 100 : $limit;

        $data = array(
            'start' => $start,
            'limit' => $limit
        );
        $content = Protocol::getAllRecord($data);
        if (empty($content))
            return false;

        return Protocol::joinCommand($this->token, $this->device_id, $this->task_id, CMD_GETNEWRECORD, 0, $content);
    }
}