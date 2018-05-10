<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-7
 * Time: 下午4:35
 * File Name: index.php
 */
?>
<?php
require('common/header.php');
?>
    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#device" aria-controls="home" role="tab" data-toggle="tab">Device
                    List</a></li>
            <li role="presentation"><a href="#user" aria-controls="profile" role="tab" data-toggle="tab">Users List</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="device">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Serial Number</th>
                        <th>Model</th>
                        <th>Token</th>
                        <th>Created Time</th>
                        <th>Is Login</th>
                        <th>User</th>
                    </tr>
                    <?php
                        $sql = 'SELECT device.*, users.email FROM device device LEFT JOIN users users ON users.id=device.user_id';
                        $result = mysql_query($sql);
                        if(mysql_num_rows($result) > 0):
                            $rowNumber = 1;
                            while($row = mysql_fetch_array($result)):
                                if($row['is_login'] == 1 && $row['lasttime'] <= time() - 30){
                                    $sql = 'UPDATE device SET is_login=0 WHERE id="'.$row['id'].'"';
                                    mysql_query($sql);
                                    $row['is_login'] = 0;
                                }
                    ?>
                        <tr>
                            <td><?php echo $rowNumber;?></td>
                            <td><a href="device.php?id=<?php echo $row['id'];?>"><?php echo $row['id'];?></a></td>
                            <td><?php echo $row['serial_number'];?></td>
                            <td><?php echo $row['model'];?></td>
                            <td><?php echo $row['token'];?></td>
                            <td><?php echo date('m/d/Y H:i', $row['createdtime']);?></td>
                            <td><font class="is_login_<?php echo $row['id'];?>"><?php echo $row['is_login'];?></td>
                            <td><?php echo $row['email'];?></td>
                        </tr>
                    <?php
                                $rowNumber++;
                            endwhile;
                        else:

                    ?>
                        <tr>
                            <td colspan="6">There has no record.</td>
                        </tr>
                    <?php
                        endif;
                    ?>
                    </thead>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="user">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Device Username</th>
                        <th>Device Password</th>
                    </tr>
                    <?php
                    $sql = 'SELECT * FROM users';
                    $result = mysql_query($sql);
                    if(mysql_num_rows($result) > 0):
                        $rowNumber = 1;
                        while($row = mysql_fetch_array($result)):
                            ?>
                            <tr>
                                <td><?php echo $rowNumber;?></td>
                                <td><?php echo $row['id'];?></td>
                                <td><?php echo $row['email'];?></td>
                                <td><?php echo $row['password'];?></td>
                                <td><?php echo $row['dusername'];?></td>
                                <td><?php echo $row['dpassword'];?></td>
                            </tr>
                            <?php
                            $rowNumber++;
                        endwhile;
                    else:

                        ?>
                        <tr>
                            <td colspan="6">There has no record.</td>
                        </tr>
                    <?php
                    endif;
                    ?>
                    </thead>
                </table>
            </div>
        </div>

    </div>
    <script type="text/javascript">
        jQuery(function(){
            setInterval(function(){
                jQuery.get('ajax.php', function(result){
                    jQuery.each(result.device, function(id, row){
                        jQuery('.is_login_'+row['id']).text(row.is_login);
                    });
                }, 'json');
            }, 10000);
        });
    </script>
<?php
require('common/footer.php');
?>