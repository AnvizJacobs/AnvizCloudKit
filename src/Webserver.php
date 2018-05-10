<?php

/**
 * File Name: Webserver.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-21
 * Time: 17:16
 * Description:
 */

defined('SRC_DIR') ? '' : define('SRC_DIR', dirname(__FILE__));

require_once(SRC_DIR . '/lib/SoapDiscovery.php');
require_once(SRC_DIR . '/lib/Tools.php');
require_once(SRC_DIR . '/lib/Montitor.php');
require_once(SRC_DIR . '/lib/Protocol.php');
require_once(SRC_DIR . '/lib/Logs.php');
require_once(SRC_DIR . '/interface.php');

class Webserver
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run($callback)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            /**
             * Generate WSDL file
             * eg. http://localhost/webserver/wsdl.html
             */

            header("Content-type: text/xml; charset=utf-8");
            $wsdl = new SoapDiscovery('Montitor', 'Webserver');
            $content = $wsdl->getWSDL();

            header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($content, '8bit') : strlen($content)));

            echo $content;
        } else {

            /** Create SOAP Server */
            $url = $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'];
            $url = $_SERVER['SERVER_PORT'] == 443 ? 'https://' . $url : 'http://' . $url;
            $options = array(
                'encoding' => 'UTF-8',
            );
            $soapServer = new SoapServer($url . "?wsdl");
            $soapServer->setClass('Montitor', $callback, $this->config);
            $soapServer->handle();
        }
    }
}