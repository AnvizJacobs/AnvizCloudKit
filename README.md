<img src="logo.png" style="height:30px;" align="right" />

# Anviz device cloud PHP interface kit

------------

### Support device
> `C2 Pro/W1/W2/OA1000`
> `FaceDeep3/FaceDeep5`
> `MBIO`

### Environmental requirements
> Apache + php(php version support 5.x/7.0)
> Extended requirements: `php_soap`/`php_mcrypt`
> support `rewrite`

### Directory File Description
* `src`             API framework
    * `lib`             Library
        * `AnvizCommand.php`    Device command function
        * `Logs.php`            Log class
        * `Montitor.php`        Listen service
        * `Protocol.php`        Protocol
        * `SoapDiscovery.php`   PHP SOAP wsdl
        * `Tools.php`           Method packets
    * `config.sample.php`          Configure file
    * `Webserver.php`       Communction process
* `sample`          Sample
    * `client`      Client
    * `server`      Server
        * `callback.php`    Callback class
        * `Webserver.php`   Soap Server
        * `.htaccess`       Route file
    * `logs`        Logs directory
    * `config.php`  Configure file
    * `database.php`DB connect
    * `db.sql`      DB file

### Testing process
1. Deploy the Apache + php environment, Install `php_soap` Extended, `php-mcrypt` Extended and `rewrite` support
2. Put the directory file to site directory, input `http://(Site domain name)/sample/server/webserver/wsdl.html` in the browser, can be display wsdl pages means successful
3. Open url `http://(site domain name)/sample/client/index.html` in brower. Register new user and record the device url, username and password
3. Power on the device,and setup device network setting connect internet
4. Input the `000048`+`OK` in device main UI to active cloud development mode.
5. Enter into `network` - `cloud` menu follow the below setup
    ```
    User Name：（User name of the 3rd step）
    Password：（Pasword of the 3rd step）
    Server IP： (http://(Site domain name)/sample/server/webserver)
    ```
6. Then on the device screen will display cloud connection ok. Open url `http://(site domain name)/sample/client/index.html` in brower, now you can see the device in page.
7. And enter into site root directory log folder to check the device communicaiton logs with server
