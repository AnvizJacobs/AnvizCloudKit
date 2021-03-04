<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午5:41
 * File Name: AnvizCommand.php
 */

/**
 * Class AnvizCommand
 * The construction method of the device command word
 */
class AnvizCommand
{
    public $device_id; //Device ID
    public $token; //Token
    public $task_id; //Task ID no more than 8 digits

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of get network information
     */
    public function getNetwork()
    {
        $content = '';
        $id      = Tools::createCommandId();

        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_GETNETWORK,
            'content' => base64_encode($content),
        );
    }

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of get Record & User & FP number
     */
    public function getRecordUserFPCount()
    {
        $content = '';
        $id      = Tools::createCommandId();

        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_GETRECORDUSERFPCOUNT,
            'content' => base64_encode($content),
        );
    }

    public function setSuperAdminPassword($password = '')
    {
        if ($password === '') {
            return false;
        }

        $content = Protocol::setSuperAdminPassword($password);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => [],
            'command' => CMD_SETADMINPASSWORD,
            'content' => base64_encode($content),
        );
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
        $data      = array(
            'year'   => date('Y', $timestamp),
            'month'  => date('m', $timestamp),
            'day'    => date('d', $timestamp),
            'hour'   => date('H', $timestamp),
            'minute' => date('i', $timestamp),
            'second' => date('s', $timestamp),
        );
        $content = Protocol::setDeviceDateTime($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_SETDATETIME,
            'content' => base64_encode($content),
        );
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
        $data  = array(
            'start' => $start,
            'limit' => $limit,
        );
        $content = Protocol::getAllEmployee($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETALLEMPLOYEE,
            'content' => base64_encode($content),
        );
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
        if (empty($idd)) {
            return false;
        }

        $content = Protocol::getEmployee($idd);
        if (empty($content)) {
            return false;
        }

        $id   = Tools::createCommandId();
        $data = array(
            'idd' => $idd,
        );
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETONEEMPLOYEE,
            'content' => base64_encode($content),
        );
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
        if (empty($employees)) {
            return false;
        }

        $content = '';
        foreach ($employees as $employee) {
            $_content = Protocol::setEmployee($employee);
            if (empty($_content)) {
                return false;
            }

            $content .= $_content;
        }
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_PUTALLEMPLOYEE,
            'content' => base64_encode($content),
        );
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
        if (empty($employee)) {
            return false;
        }

        $content = '';
        foreach ($employee as $row) {
            $content .= Protocol::setEmployee($row);
        }

        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_PUTONEEMPLOYEE,
            'content' => base64_encode($content),
        );
    }

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of clear all employee (And clear all fingerprints, but not clear attendance records)
     */
    public function clearEmployee()
    {

        $content = '';

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_DELETEALLEMPLOYEE,
            'content' => base64_encode($content),
        );
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
        if (empty($idds)) {
            return false;
        }

        $content = '';
        $data    = array();
        foreach ($idds as $idd) {
            $_content = Protocol::delEmployee($idd);
            if (empty($_content)) {
                return false;
            }

            $content .= $_content;
            $data['idd'][] = $idd;
        }
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_DELETEONEEMPLOYEE,
            'content' => base64_encode($content),
        );
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
        $data  = array(
            'start' => $start,
            'limit' => $limit,
        );
        $content = Protocol::getAllFinger($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETALLFINGER,
            'content' => base64_encode($content),
        );
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
        if (empty($idd)) {
            return false;
        }

        $content = Protocol::getFinger($idd);
        if (empty($content)) {
            return false;
        }

        $data['idd'] = $idd;
        $id          = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETONEFINGER,
            'content' => base64_encode($content),
        );
    }

    /**
     * @param $idd
     * @return bool|string
     * @description The command of download the specify employee face
     * @params
     *      idd：Employee ID on device
     */
    public function getFace($idd)
    {
        return $this->getFinger($idd);

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
        if (empty($fingers)) {
            return false;
        }

        $content = '';
        foreach ($fingers as $finger) {
            $_content = Protocol::setFinger($finger);
            if (empty($_content)) {
                continue;
            }

            $content .= $_content;
        }
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_PUTALLFINGER,
            'content' => base64_encode($content),
        );
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
        if (empty($finger)) {
            return false;
        }

        $content = Protocol::setFinger($finger);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_PUTONEFINGER,
            'content' => base64_encode($content),
        );
    }


    /**
     * @author jacobs@anviz.com
     * @param $face
     * @return bool|string
     * @description The command of upload one face
     * @params
     *      face: 人脸信息
     *           array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'sign' => 20,            //Face sign is 20
     *                      'template' => (BLOB)    //Face template. The field type select blob type when save to database
     *                  ),
     */
    public function setFace($face)
    {
        if (empty($face)) {
            return false;
        }

        $content = Protocol::setFace($face);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_PUTONEFINGER,
            'content' => base64_encode($content),
        );
    }


    /**
     * @author jacobs@anviz.com
     * @param $idd
     * @return bool|string
     * @description The command of remote enroll face
     * @params
     *      idd: Employee ID on device
     */
    public function setEnrollFace($idd)
    {
        if (empty($idd)) {
            return false;
        }

        $data   = array(
            'idd'    => $idd,
            'sign'   => 20,
        );
        $content = Protocol::setEnrollFinger($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_ENROLLFINGER,
            'content' => base64_encode($content),
        );
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
        if (empty($idd)) {
            return false;
        }

        $sign   = empty($sign) ? 0 : $sign;
        $signed = empty($signed) ? 0 : $signed;
        $data   = array(
            'idd'    => $idd,
            'sign'   => $sign,
            'signed' => $signed,
        );
        $content = Protocol::setEnrollFinger($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_ENROLLFINGER,
            'content' => base64_encode($content),
        );
    }

    public function setEnrollCard($idd = '')
    {
        if (empty($idd)) {
            return false;
        }
        $data   = array(
            'idd'    => $idd
        );

        $content = Protocol::setEnrollCard($idd);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_ENROLLCARD,
            'content' => base64_encode($content),
        );
    }

    /**
     * @author jacobs@anviz.com
     * @return bool|string
     * @description The command of clear all fingerprint (but not clear employee and attendance record)
     */
    public function clearFinger()
    {
        $content = '';

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => array(),
            'command' => CMD_DELETEALLFINGER,
            'content' => base64_encode($content),
        );
    }

    /**
     * @return bool|string
     * @description The command of clear all face (but not clear employee and attendance record)
     */
    public function clearFace(){
        return $this->clearFinger();
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
        if (empty($idd)) {
            return false;
        }

        $sign = empty($sign) ? 0 : $sign;
        $data = array(
            'idd'  => $idd,
            'sign' => $sign,
        );
        $content = Protocol::deleteFinger($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_DELETEONEFINGER,
            'content' => base64_encode($content),
        );
    }

    /**
     * @param $idd
     * @return bool|string
     * @description The command of delete the specify employee face(But not delete employee and attendance record)
     * @params
     *      idd: Employee ID on device
     */
    public function deleteFace($idd)
    {
        return $this->deleteFinger($idd, 20);
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
        $data  = array(
            'start' => $start,
            'limit' => $limit,
        );
        $content = Protocol::getAllRecord($data);
        $id      = Tools::createCommandId();

        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETALLRECORD,
            'content' => base64_encode($content),
        );
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
        $data  = array(
            'start' => $start,
            'limit' => $limit,
        );
        $content = Protocol::getAllRecord($data);
        if (empty($content)) {
            return false;
        }

        $id = Tools::createCommandId();
        return array(
            'id'      => $id,
            'params'  => $data,
            'command' => CMD_GETNEWRECORD,
            'content' => base64_encode($content),
        );
    }
}
