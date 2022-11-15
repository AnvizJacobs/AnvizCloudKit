<img src="logo.png" style="height:30px;" align="right" />

# 安威士设备云接口PHP开发套件

------------

### 支持设备
> `C2 Pro/W1/W2/OA1000`
> `FaceDeep3/FaceDeep5/FaceDeep3-IRT/FaceDeep5-IRT/FacePass 7 Pro`
> `MBIO`

### 环境要求
> Apache + php(php版本为5.x/7.0)
> 扩展要求： php_soap/php-mcrypt
> 支持rewrite

### 目录文件说明
* `src`             设备通讯框架
    * `lib`             开发库
        * `AnvizCommand.php`    设备指令方法
        * `Logs.php`            日志方法
        * `Montitor.php`        设备通讯监听服务
        * `Protocol.php`        协议相关
        * `SoapDiscovery.php`   PHP SOAP的wsdl生成类
        * `Tools.php`           常用方法封包
    * `config.sample.php`          配置文件示例
    * `Webserver.php`       通讯处理类
* `sample`          示例
    * `client`      客户端
    * `server`      服务端
        * `callback.php`    服务端设备通讯回调方法
        * `Webserver.php`   Soap Server
        * `.htaccess`       路由配置文件
    * `logs`        日志目录
    * `config.php`  配置文件
    * `database.php`数据库连接
    * `db.sql`      数据库导入文件

### 测试流程
1. 部署Apache+php环境，安装`php_soap`扩展, `php-mcrypt`扩展和`rewrite`的支持
2. 将该目录下的文件放入到站点目录，在浏览器中打开`http://(站点域名)/sample/server/webserver/wsdl.html`，能正常显示wsdl页面即表示运行成功
3. 打开浏览器访问`http://(站点域名)/sample/client/index.html`，注册新用户，记录成功后显示的设备连接地址、用户名和密码
4. 打开设备电源，在设备上操作进入设备配置界面，配置设备网络连接方式及IP地址信息接入网络
5. 在设备主界面直接输入`000048`，然后按`ok`键开启开发者模式。
6. 在设备上依次进入`网络设置`->`云`菜单，安装以下实例进行配置，配置完成后退出：
    ```
    用户：（第3步的用户名）
    密码：（第3步的密码）
    服务器IP： ((站点域名)/sample/server/webserver)
    ```
7. 当设备主界面上云图标显示正常后，说明设备与服务器通讯正常。打开浏览器访问`http://(站点域名)/sample/client/index.html`，可以看到已经连接的设备
8. 可以进入示例目录下的log文件夹查看设备与服务器的通讯日志
