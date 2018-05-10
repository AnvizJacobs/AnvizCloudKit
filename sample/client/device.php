<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午2:08
 * File Name: device.php
 */
?>
<?php
require('common/header.php');

$id = $_REQUEST['id'];
if (empty($id)) {
    header('Location: index.php');
}

$sql = 'SELECT device.*, users.email from device device LEFT JOIN users users ON users.id=device.user_id WHERE device.id="' . $id . '"';
$result = mysql_query($sql);
if (mysql_num_rows($result) <= 0) {
    header('Location: index.php');
}

$device = mysql_fetch_array($result);
?>
<div class="panel panel-default">
    <div class="panel-heading">Device Information</div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Device ID</th>
                    <td><?php echo $device['id']; ?></td>
                    <th>Serail NUmber</th>
                    <td><?php echo $device['serial_number']; ?></td>
                </tr>
                <tr>
                    <th>Model</th>
                    <td><?php echo $device['model']; ?></td>
                    <th>Firmware</th>
                    <td><?php echo $device['firmware']; ?></td>
                </tr>
                <tr>
                    <th>Protocol</th>
                    <td><?php echo $device['protocol']; ?></td>
                    <th>Token</th>
                    <td><?php echo $device['token']; ?></td>
                </tr>
                <tr>
                    <th>Created Time</th>
                    <td><?php echo date('m/d/Y H:i', $device['createdtime']); ?></td>
                    <th>User</th>
                    <td><?php echo $device['email']; ?></td>
                </tr>
                <tr>
                    <th>Last Time</th>
                    <td>
                        <font class="lasttime_<?php echo $device['id']; ?>"><?php echo date('m/d/Y H:i:s', $device['lasttime']); ?></font>
                    </td>
                    <th>Is Login</th>
                    <td><font class="is_login_<?php echo $device['id']; ?>"><?php echo $device['is_login']; ?></font>
                    </td>
                </tr>
            </table>
        </div>
        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#records" aria-controls="home" role="tab"
                                                          data-toggle="tab">Records</a></li>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <input type="hidden" id="device_id" value="<?php echo $device['id']; ?>"/>
                <div role="tabpanel" class="tab-pane active" id="records" style="padding: 10px;">
                    <div>
                        <button id="btnDownloadAllRecords" type="button" class="btn btn-primary">Download All Records
                        </button>
                    </div>
                    <div id="RecordsList">
                        <?php require (dirname(__FILE__).'/common/recordlist.php');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.intervalDeviceStatus();
    jQuery(function () {
        jQuery('#btnDownloadAllRecords').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'downloadAllRecords'}, function (result) {
                if(!result.success){
                    alert('Error');
                    _this.button('reset');
                }else{
                    app.checkCommandStatus(result.data.id).done(function(){
                        jQuery('#RecordsList').load('common/recordlist.php', {id:device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });
    });
</script>
<?php
require('common/footer.php');
?>
