<?php

/**
 * File Name: Webserver.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-22
 * Time: 9:07
 * Description:
 */

require_once(PATH . '/lib/Tools.php');
require_once(PATH . '/lib/Protocol.php');
require_once(CALLBACK_FILE);
$callback_name = CALLBACK_CLASS;
$callback = new $callback_name();

/**
 * Class Webserver
 */
class Webserver
{
    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionRegister
     * @param string $data
     * @Description: Register Device
     */
    public function actionRegister($data = "")
    {
        global $callback;

        Tools::log('debug', 'actionRegister: Data - ' . $data);
        if (empty($data)) {
            Tools::log('error', 'actionRegister: Receive Data is NULL');
            return false;
        }

        $result = Protocol::RegisterDevice($data);
        if (!$result || empty($result['serial_number'])) {
            Tools::log('error', 'actionRegister: Register fail');
            return false;
        }

        if (!empty($callback) && method_exists($callback, 'createId')) {
            $device_id = $callback->createId($result);
        } else {
            $device_id = Tools::uuid();
        }
        Tools::log('debug', 'actionRegister: Device ID - ' . $device_id);

        if (!empty($callback) && method_exists($callback, 'createToken')) {
            $token = $callback->createToken($device_id);
        } else {
            $token = Tools::randomkey(8);
        }
        Tools::log('debug', 'actionRegister: RandomKey - ' . $token);

        /** Return to let device to login system */
        $command = Protocol::joinCommand($token, $device_id, '11111111', CMD_LOGIN, 0, 32, $device_id);

        return Tools::R($token . $command);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionTransport
     * @param string $serial_number
     * @param string $data
     * @Description:Transport
     */
    public function actionTransport($serial_number = "", $data = "")
    {
        global $callback;

        Tools::log('debug', 'actionTransport: Device - ' . $serial_number . '; Data - ' . $data);

        $device_id = $serial_number;

        if (empty($device_id) || empty($data)) {
            Tools::log('error', 'actionTransport: The lack of necessary parameters');

            return Protocol::showRegister($device_id);
        }

        if (!empty($callback) && method_exists($callback, 'getToken')) {
            $token = $callback->getToken($device_id);
        } else {
            return Protocol::showRegister($device_id);
        }

        if (!empty($callback) && method_exists($callback, 'validToken')) {
            if (!$callback->validToken($token, $device_id)) {
                Tools::log('error', 'actionTransport: The token has expires');

                return Protocol::showRegister($device_id);
            }
        } else {
            return Protocol::showRegister($device_id);
        }

        $data = Protocol::explodeCommand($token, $data);
        if (!$data) {
            Tools::log('error', 'actionTransport: The token has expires');

            return Protocol::showRegister($device_id);
        }

        Tools::log('debug', 'actionTransport: Command - ' . $data['command'] . ', Data - ' . json_encode($data));
        switch ($data['command']) {
            case CMD_REGESTER:
                return Protocol::showRegister($device_id);
                break;
            case CMD_LOGIN:
                $result = Protocol::LoginDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'Login')) {
                    if (!$callback->Login($device_id, $data['id'], $result['username'], $result['password']))
                        return Protocol::showForbidden();
                }
                break;
            case CMD_GETNETWORK:
                $result = Protocol::NetworkDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'network')) {
                    if (!$callback->network($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            case CMD_GETALLEMPLOYEE:
            case CMD_GETONEEMPLOYEE:
                $result = Protocol::EmployeeDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'employee')) {
                    if (!$callback->employee($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            case CMD_GETALLFINGER:
            case CMD_GETONEFINGER:
                $result = Protocol::FingerDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'finger')) {
                    if (!$callback->finger($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            case CMD_ENROLLFINGER:
                $result = Protocol::EnrollFinger($data['content']);
                if (!empty($callback) && method_exists($callback, 'enrollFinger')) {
                    if (!$callback->enrollFinger($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            case CMD_GETALLRECORD:
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'record')) {
                    if (!$callback->record($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            default:
                break;
        }
        Tools::log('debug', 'actionTransport: ' . $data['command'] . ' - ' . json_encode($result));

        /** Get the next command **/
        if (!empty($callback) && method_exists($callback, 'getNextCommand')) {
            $command = $callback->getNextCommand($device_id);
        }
        if (empty($command)) {
            $command = Protocol::showNocommand($token, $device_id);
        }

        return Tools::R($command);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionReport
     * @param string $serial_number
     * @param string $data
     * @Description: Report
     */
    public function actionReport($serial_number = "", $data = "")
    {
        global $callback;

        Tools::log('debug', 'actionReport: Device - ' . $serial_number . '; Data - ' . $data);

        $device_id = $serial_number;

        if (empty($device_id) || empty($data)) {
            Tools::log('error', 'actionReport: The lack of necessary parameters');

            return Protocol::showRegister($device_id);
        }

        if (!empty($callback) && method_exists($callback, 'getToken')) {
            $token = $callback->getToken($device_id);
        } else {
            return Protocol::showRegister($device_id);
        }

        if (!empty($callback) && method_exists($callback, 'validToken')) {
            if (!$callback->validToken($token, $device_id)) {
                Tools::log('error', 'actionReport: The token has expires');

                return Protocol::showRegister($device_id);
            }
        } else {
            return Protocol::showRegister($device_id);
        }

        $data = Protocol::explodeCommand($token, $data);
        if (!$data) {
            Tools::log('error', 'actionReport: The token has expires');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
            return Tools::R($command);
        }

        Tools::log('debug', 'actionReport: Data - ' . json_encode($data));
        switch ($data['command']) {
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                if (!empty($callback) && method_exists($callback, 'realRecord')) {
                    if (!$callback->realRecord($device_id, $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                    }
                }
                break;
            default:
                break;
        }
        Tools::log('debug', 'actionReport: ' . $data['command'] . ' - ' . json_encode($result));

        /** Get the next command **/
        if (!empty($callback) && method_exists($callback, 'getNextCommand')) {
            $command = $callback->getNextCommand($device_id);
        }
        if (empty($command)) {
            $command = Protocol::showNocommand($token, $device_id);
        }

        return Tools::R($command);
    }
}