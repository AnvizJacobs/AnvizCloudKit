<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 下午4:59
 * File Name: Logs.php
 */

class Logs
{
    private $debug = true;
    private $log_path;

    public function __construct($config)
    {
        if (!empty($config['debug']))
            $this->debug = $config['debug'];
        if (!empty($config['path']))
            $this->log_path = $config['path'];
        else
            $this->log_path = SRC_DIR . DIRECTORY_SEPARATOR . 'logs';
    }

    /**
     * @param string $type
     * @param string $message
     * @return bool
     */
    public function write($type = 'error', $message = '')
    {
        if (!$this->debug && ($type != 'error' || $type != 'warning')) {
            return true;
        }

        if (!is_dir($this->log_path) && !mkdir($this->log_path, '0777'))
            return false;

        if (is_array($message) || is_object($message))
            $message = json_encode($message);

        $filename = 'log_' . date('Ymd-H') . '.log';

        $fp = fopen($this->log_path . DIRECTORY_SEPARATOR . $filename, 'a+');

        fwrite($fp, date("m/d/Y H:i:s"));
        fwrite($fp, "\t");
        fwrite($fp, ucfirst($type));
        fwrite($fp, "\t");
        fwrite($fp, $message);
        fwrite($fp, "\r\n");

        fclose($fp);

        return true;
    }
}