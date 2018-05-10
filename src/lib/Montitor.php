<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 下午4:30
 * File Name: Montitor.php
 */

class Montitor
{
    public $callback;

    private $log;

    public function __construct($callback, $config = array())
    {
        $this->callback = $callback;

        $this->log = new Logs($config['logs']);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionRegister
     * @param string $data
     * @Description: Register Device
     */
    public function actionRegister($data = "")
    {
        if (empty($data)) {
            $this->log->write('error', 'actionRegister: Receive Data is NULL');
            return false;
        }

        $result = Protocol::RegisterDevice($data);
        if (!$result || empty($result['serial_number'])) {
            $this->log->write('error', 'actionRegister: Register fail');
            return false;
        }

        $device = $this->callback->register($result);

        if (empty($device['id']) || empty($device['token'])) {
            $this->log->write('error', 'actionRegister: Register fail');
            return false;
        }

        $token = $device['token'];
        $id = $device['id'];
        $this->log->write('info', 'actionRegister: Register Success!');

        /** Return to let device to login system */
        $command = Protocol::joinCommand($token, $id, '11111111', CMD_LOGIN, 0, $id);

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
        $device_id = $serial_number;

        if (empty($device_id) || empty($data)) {
            $this->log->write('error', 'actionTransport: The lack of necessary parameters');

            return Protocol::showRegister($device_id);
        }

        $token = $this->callback->getToken($device_id);
        $this->log->write('debug', 'actionTransport: Get Token:' . $token);

        if (empty($token)) {
            $this->log->write('error', 'actionTransport: The token has expires');
            return Protocol::showRegister($device_id);
        }

        $data = Protocol::explodeCommand($token, $data);
        if (!$data) {
            $this->log->write('error', 'actionTransport: The token has expires');

            return Protocol::showRegister($device_id);
        }

        $this->callback->updateLastlogin($device_id);

        $this->log->write('debug', 'actionTransport: Command - ' . $data['command'] . ', Data - ' . json_encode($data));
        switch ($data['command']) {
            case CMD_REGESTER:
                return Protocol::showRegister($device_id);
                break;
            case CMD_LOGIN:
                $result = Protocol::LoginDevice($data['content']);
                if (!$this->callback->login($device_id, $result['username'], $result['dpassword'])) {
                    return Protocol::showForbidden();
                }
                break;
            case CMD_GETNETWORK:
                $result = Protocol::NetworkDevice($data['content']);
                if (!$this->callback->network($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                }
                break;
            case CMD_GETALLEMPLOYEE:
            case CMD_GETONEEMPLOYEE:
                $result = Protocol::EmployeeDevice($data['content']);
                if (!$this->callback->employee($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLEMPLOYEE);
                }
                break;
            case CMD_GETALLFINGER:
            case CMD_GETONEFINGER:
                $result = Protocol::FingerDevice($data['content']);
                if (!$this->callback->finger($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLFINGER);
                }
                break;
            case CMD_ENROLLFINGER:
                $result = Protocol::EnrollFinger($data['content']);
                if (!$this->callback->enrollFinger($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_ENROLLFINGER);
                }
                break;
            case CMD_GETALLRECORD:
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                if (!$this->callback->record($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLRECORD);
                }
                break;
            default:
                break;
        }
        /** Get the next command **/
        $data = $this->callback->getNextCommand($device_id);
        if (empty($data)) {
            $command = Protocol::showNocommand($token, $device_id);
        }else{
            $command = Protocol::joinCommand($token, $device_id, $data['id'], $data['command'], 0, $data['content']);
        }

        return Tools::R($command);
    }
}