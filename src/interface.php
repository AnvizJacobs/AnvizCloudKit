<?php

/**
 * Created by Jacobs.
 * Auth: jacobs@anviz.com
 * Copyright: Anviz Global Inc.
 * Date: 2017/9/19
 * Time: 11:08
 * FileName: interface.php
 */

/**
 * Interface AnvizInterface
 * The callback class for the device response server instruction
 */
interface AnvizInterface
{
    /**
     * @return
     *      array(
     *          'device_id' => '123abc123abc',
     *          'token' => '12345678'
     *      );
     *      device_id: string Device ID
     *      token: int Token no more than 8 digits
     * @description When the device register to server, will trigger this methods
     * @params
     *       $data = array(
     *          'serial_number' => '1234567890123456',
     *          'model' => 'C2 Pro',
     *          'firmware' => '02.12.38',
     *          'protocol' => 'V1.0'
     *      );
     *      serial_number: int Serial number
     *      model: string Device model
     *      firmware: string Firmware version
     *      protocol: string Protocol version
     */
    public function register($data);

    /**
     * @return string Device Token
     * @description Query token by device ID
     * @param string $id Device ID
     */
    public function getToken($id);

    /**
     * @return boolean
     * @description Login authentication
     * @param
     *      $id string Device ID
     *      $dusername string Device username
     *      $dpassword string Device password
     */
    public function login($id, $dusername = '', $dpassword = '');

    /**
     * @return boolean
     * @description Login authentication
     * @param
     *      $id string Device ID
     *      $command_id command_id
     */
    public function total($id, $command_id, $data);

    /**
     * @return boolean
     * @description Get records. By getRecords/getNewRecords will call back this method
     * @param
     *      id: string Device ID
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
     */
    public function record($id, $command_id, $data);

    /**
     * @return boolean
     * @description Get temperature records. By temperatureRecord will call back this method
     * @param
     *      id: string Device ID
     *      task_id: TASK ID
     *      records: array
     *              array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'checktime' => 1356879141,  //Attendance time. Unix timestamp
     *                      'mask' => 0,          //mask
     *                      'temperature' => 37.0,          //temperature
     *                  ),
     *                  array(
     *                      'idd' => 1001,
     *                      'checktime' => 1356879141,
     *                      'mask' => 0,          //mask
     *                      'temperature' => 37.0,          //temperature
     *                  ),
     *              )
     */
    public function temperatureRecord($id, $command_id, $data);

    /**
     * @return boolean
     * @description Get Employee. By getEmployee will call back this method
     * @param
     *      id: string Device ID
     *      task_id: TASK ID
     *      records: array
     *              array(
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'name' => 'demo',  //Attendance time. Unix timestamp
     *                      'cardid' => '1234567890', //Card ID
     *                      'passd' => '',  //Attendance password by Employee ID,
     *                      'group_id' => '',   //The accesscontrol group id,
     *                      'fingersign' => '',
     *                      'is_admin' => 0,
     *                  ),
     *                  array(
     *                      'idd' => 1001,          //Employee ID on device
     *                      'name' => 'demo',  //Attendance time. Unix timestamp
     *                      'cardid' => '1234567890', //Card ID
     *                      'passd' => '',  //Attendance password by Employee ID,
     *                      'group_id' => '',   //The accesscontrol group id,
     *                      'fingersign' => '',
     *                      'is_admin' => 0,
     *                  ),
     *              )
     */
    public function employee($id, $command_id, $data);

    public function finger($id, $command_id, $data);

    public function enrollFinger($id, $command_id, $data);

    public function enrollCard($id, $command_id, $data);

    /**
     * @return boolean
     * @description Every time the device links with the server, it will start the event.
     * @param
     *      id: string Device ID
     */
    public function updateLastlogin($id);

    /**
     * @return string
     * @description Each time the device communicates with the server, this method is called to return the instructions that the device needs to execute.
     *              If return null, the device will execute next heartbeat after 5seconds.
     *              Instructions in the package method see the following AnvizCommand class method
     * @param
     *      id: string Device id
     */
    public function getNextCommand($id);
}