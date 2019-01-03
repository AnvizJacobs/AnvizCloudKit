<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 上午9:09
 * File Name: register.php
 */
?>
<?php
require('common/header.php');
if ($_POST) {
    if (empty($_POST['email'])) {
        $errorMsg = 'Please enter the email address';
    }elseif(empty($_POST['password'])){
        $errorMsg = 'Please enter the password';
    }else{
        $sql = 'SELECT count(*) as number FROM users WHERE email="'.$_POST['email'].'"';
        $result = $db->query($sql);
        $row = $db->fetch_array($result);
        if($row['number'] > 0){
            $errorMsg = 'The email has been already';
        }else{
            $dusename = rand(10000000, 99999999);
            $dpassword = rand(10000000, 99999999);
            $sql = 'INSERT INTO users(email, password, dusername, dpassword) VALUES ("' . $_POST['email'] . '","' . $_POST['password'] . '", "'.$dusename.'", "'.$dpassword.'")';
            $db->query($sql);


            if($_SERVER['SERVER_PORT'] == 443){
                $url = 'https://';
            }else{
                $url = 'http://';
            }
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $_url = explode('/sample/', $url);

            $device_url = $_url[0].'/sample/server/';

            $successMsg = 'Register successfully!<br />';
            $successMsg .= 'Device connetct infomation: <br />';
            $successMsg .= 'URL:'.$device_url.'<br />';
            $successMsg .= 'Username:'.$dusename.'<br />';
            $successMsg .= 'Password:'.$dpassword;
        }
    }
}
?>
<form action="register.php" method="post">
    <?php if(!empty($errorMsg)):?>
    <div class="alert alert-danger" role="alert"><?php echo $errorMsg;?></div>
    <?php endif;?>
    <?php if(!empty($successMsg)):?>
    <div class="alert alert-success" role="alert"><?php echo $successMsg;?></div>
    <?php endif;?>
    <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Email" value="<?php echo $_POST['email'];?>">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="text" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php echo $_POST['password'];?>">
    </div>
    <button type="submit" class="btn btn-default">Register</button>
</form>
<?php
require('common/footer.php');
?>
