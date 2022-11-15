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
        global $log;

        $this->callback = $callback;

        $this->log = $log;
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

        $this->log->write('info', 'actionRegister: Device Info:' . json_encode($result));
        $device = $this->callback->register($result);

        if (empty($device['id']) || empty($device['token'])) {
            $this->log->write('error', 'actionRegister: Register fail');
            return false;
        }

        $token = $device['token'];
        $id    = $device['id'];
        $this->log->write('info', 'actionRegister: Register Success!');
        $this->log->write('info', 'actionRegister: Device Info:' . json_encode($device));

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
        $this->log->write('debug', 'actionTransport: :' . $device_id);
        $this->log->write('debug', 'actionTransport: DATA:' . $data);

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

        $this->log->write('debug', 'explodeCommand: Data-'.json_encode($data));
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
                $this->log->write('debug', 'actionTransport: ' . CMD_GETNETWORK . ' - ' . json_encode($result));
                if (!$this->callback->network($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                }
                break;
            case CMD_GETRECORDUSERFPCOUNT:
                $result = Protocol::RecordUserFPCountDevice($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETRECORDUSERFPCOUNT . ' - ' . json_encode($result));
                if (!$this->callback->total($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNETWORK);
                }
                break;
            case CMD_GETALLEMPLOYEE:
            case CMD_GETONEEMPLOYEE:
                $result = Protocol::EmployeeDevice($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETONEEMPLOYEE . ' - ' . json_encode($result));
                if (!$this->callback->employee($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLEMPLOYEE);
                }
                break;
            case CMD_GETALLFINGER:
            case CMD_GETONEFINGER:

                $dataIsFace = Protocol::dataIsFace($data['content']);
                $this->log->write('debug', 'device is face: ' . intval($dataIsFace));
                $result = $dataIsFace ? Protocol::FaceDevice($data['content']) : Protocol::FingerDevice($data['content']);

                $this->log->write('debug', 'actionTransport: ' . CMD_GETALLFINGER . ' - ' . json_encode($result));

                if ($dataIsFace) {
                    if (!$this->callback->face($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETALLFINGER);
                    }
                } else {
                    if (!$this->callback->finger($device_id, $data['id'], $result)) {
                        return Protocol::showError($token, $device_id, CMD_GETALLFINGER);
                    }
                }
                break;
            case CMD_ENROLLFINGER:

                $result = Protocol::dataIsFace($data['content'])?
                    Protocol::EnrollFace($data['content']):Protocol::EnrollFinger($data['content']);

                $this->log->write('debug', 'actionTransport: ' . CMD_ENROLLFINGER . ' - ' . $result['idd'].' - '.$result['temp_id']);
                if (!$this->callback->enrollFinger($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_ENROLLFINGER);
                }
                break;
            case CMD_ENROLLCARD:
                $result = Protocol::EnrollCardDevice($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_ENROLLCARD . ' - ' . json_encode($result));
                if (!$this->callback->enrollCard($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_ENROLLFINGER);
                }
                break;
            case CMD_GETALLRECORD:
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETALLRECORD . ' - ' . json_encode($result));
                if (!$this->callback->record($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLRECORD);
                }
                break;

            case CMD_GETNEWTEMPRECORD:
                $result = Protocol::TemperatureRecordDevice($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETNEWTEMPRECORD . ' - ' . json_encode($result));
                if (!$this->callback->temperatureRecord($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNEWTEMPRECORD);
                }
                break;

            case CMD_GETTEMPRECORDPIC:
                $result = Protocol::TemperaturePic($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETTEMPRECORDPIC );
                if (!$this->callback->temperaturePic($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNEWTEMPRECORD);
                }
                break;

            default:
                if (!$this->callback->other($device_id, $data['id'])) {
                    return Protocol::showError($token, $device_id, CMD_GETALLRECORD);
                }
                break;
        }

        /** Get the next command **/
        $data = $this->callback->getNextCommand($device_id);
        if (empty($data)) {
            $command = Protocol::showNocommand($token, $device_id);
        } else {
            $this->log->write('debug', 'Next Command: Data:' . json_encode($data));
            $command = Protocol::joinCommand($token, $device_id, $data['id'], $data['command'], 0, $data['content']);
        }

        return Tools::R($command);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionReport
     * @param string $serial_number
     * @param string $data
     * @Description:Transport
     */
    public function actionReport($serial_number = "", $data = "")
    {
        $device_id = $serial_number;

        if (empty($device_id) || empty($data)) {
            $this->log->write('error', 'actionReport: The lack of necessary parameters');

            return Protocol::showRegister($device_id);
        }

        $token = $this->callback->getToken($device_id);
        $this->log->write('debug', 'actionReport: Get Token:' . $token);

        if (empty($token)) {
            $this->log->write('error', 'actionReport: The token has expires');
            return Protocol::showRegister($device_id);
        }
        $data = Protocol::explodeCommand($token, $data);
        if (!$data) {
            $this->log->write('error', 'actionReport: The token has expires');

            return Protocol::showRegister($device_id);
        }

        $this->callback->updateLastlogin($device_id);

        switch ($data['command']) {
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                $this->log->write('debug', 'actionReport: Command - ' . $data['command'] . ', Data - ' . json_encode($result));
                if (!$this->callback->record($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETALLRECORD);
                }
                break;

            case CMD_GETNEWTEMPRECORD:
                $result = Protocol::TemperatureRecordDevice($data['content']);
                $this->log->write('debug', 'actionReport: Command - ' . $data['command'] . ', Data - ' . json_encode($result));
                if (!$this->callback->temperatureRecord($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNEWTEMPRECORD);
                }
                break;

            case CMD_GETTEMPRECORDPIC:
                $result = Protocol::TemperaturePic($data['content']);
                $this->log->write('debug', 'actionTransport: ' . CMD_GETTEMPRECORDPIC );
                if (!$this->callback->temperaturePic($device_id, $data['id'], $result)) {
                    return Protocol::showError($token, $device_id, CMD_GETNEWTEMPRECORD);
                }
                break;
            default:
                if (!$this->callback->other($device_id, $data['id'])) {
                    return Protocol::showError($token, $device_id, CMD_GETALLRECORD);
                }
                break;
        }

        /** Get the next command **/
        $data = $this->callback->getNextCommand($device_id);
        if (empty($data)) {
            $command = Protocol::showNocommand($token, $device_id);
        } else {
            $this->log->write('debug', 'actionTransport: Response:' . json_encode($data));
            $command = Protocol::joinCommand($token, $device_id, $data['id'], $data['command'], 0, $data['content']);
        }

        return Tools::R($command);
    }


}
