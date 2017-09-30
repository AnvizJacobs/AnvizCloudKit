<?php
/**
 * Created by Jacobs.
 * Auth: jacobs@anviz.com
 * Copyright: Anviz Global Inc.
 * Date: 2017/9/19
 * Time: 10:50
 * FileName: index.php
 */

require_once(dirname(__FILE__) . '/../interface.php');

class callback implements AnvizInterface
{
    public function createId($device)
    {
        //Demo Return
        return 'ffad05f38cbd0e28afea7ea4f8628e2e';
    }

    public function createToken($device_id)
    {
        //Demo Return
        return '12345678';
    }

    public function getToken($device_id)
    {
        //Demo Return
        return '12345678';
    }

    public function validToken($token, $device_id)
    {

        return true;
    }

    public function Login($device_id, $task_id, $username, $password)
    {
        return true;
    }

    public function network($device_id, $task_id, $network)
    {
        return true;
    }

    public function employee($device_id, $task_id, $employees)
    {

        return true;
    }

    public function finger($device_id, $task_id, $fingers)
    {

        return true;
    }

    public function enrollFinger($device_id, $task_id, $finger)
    {

        return true;
    }

    public function record($device_id, $task_id, $records)
    {

        return true;
    }

    public function realRecord($device_id, $records)
    {

        return true;
    }

    public function getNextCommand($device_id)
    {
        $AnvizCommand = new AnvizCommand();
        $AnvizCommand->token = $this->getToken($device_id);
        $AnvizCommand->device_id = $device_id;
        $AnvizCommand->task_id = substr(time(), -8);
        $finger = array(
                'idd' => 1002,
                'sign' => 0,
                'fp' => file_get_contents(dirname(__FILE__).'/1')
        );
        return $AnvizCommand->clearFinger();
    }
}