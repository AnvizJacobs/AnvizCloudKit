<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-8
 * Time: 下午2:08
 * File Name: device.php
 */
?>
<?php
require 'common/header.php';

$id = $_REQUEST['id'];
if (empty($id)) {
    header('Location: index.php');
}

$sql    = 'SELECT device.*, users.email from device device LEFT JOIN users users ON users.id=device.user_id WHERE device.id="' . $id . '"';
$result = $db->query($sql);
if ($db->num_rows($result) <= 0) {
    header('Location: index.php');
}

$device = $db->fetch_array($result);
?>
<div class="panel panel-default">
    <div class="panel-heading">Device Information</div>
    <div class="panel-body">
        <div class="table-responsive">

            <table class="table table-bordered">
                <tr>
                    <td colspan="6"><button id="btnOpendoor" type="button" class="btn btn-primary">Opendoor</button></td>
                </tr>
            </table>

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
                <tr>
                    <td colspan="4"><button id="btnGetRecordUserFPCount" type="button" class="btn btn-primary">Get User &amp; Record number</button></td>
                </tr>
                <tr>
                    <th>Total of User</th>
                    <td>
                        <font class="userCount">-</font>
                    </td>
                    <th>Total of FP</th>
                    <td><font class="fpCount">-</font>
                    </td>
                </tr>
                <tr>
                    <th>Total of Record</th>
                    <td>
                        <font class="recordCount">-</font>
                    </td>
                    <th>Last Update Time</th>
                    <td><font class="lasttimeCount">-</font>
                    </td>
                </tr>
                <tr>
                    <th>Super Admin password</th>
                    <td colspan="3">
                        <input type="text" name="superadminpassword" maxlength="6" /> &nbsp; <button id="btnSetAdminPassWord" type="button" class="btn btn-primary">Modify Super Admin password</button>
                    </td>
                </tr>
            </table>
        </div>


        <div class="table-responsive">

            <table class="table table-bordered">
                <tr>
                    <th>Mask Detection</th>
                    <td>
                        <div class="btn-group">
                            <button id="MaskDetection" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">No Mask Wearing Alarm
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="MaskDetection" data-id="0">Off</a></li>
                                <li><a href="" class="MaskDetection" data-id="1">No Mask Wearing Alarm</a></li>
                                <li><a href="" class="MaskDetection" data-id="2">Wear Mask allow to Access</a></li>
                            </ul>
                        </div>
                    </td>

                    <th>Mask Alarm</th>
                    <td>
                        <div class="btn-group">
                            <button id="MaskAlarm" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Enable
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="MaskAlarm" data-id="0">Off</a></li>
                                <li><a href="" class="MaskAlarm" data-id="1">Enable</a></li>
                            </ul>
                        </div>
                    </td>


                    <th>Work Mode</th>
                    <td>
                        <div class="btn-group">
                            <button id="WorkMode" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Normal Mode
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="WorkMode" data-id="0">Disable Temperature Detection</a></li>
                                <li><a href="" class="WorkMode" data-id="1">Normal Mode</a></li>
                                <li><a href="" class="WorkMode" data-id="2">Visitor Mode</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>Relay Control</th>
                    <td>
                        <div class="btn-group">
                            <button id="RelayControl" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Fever Alarm Output
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="RelayControl" data-id="0">Door Open</a></li>
                                <li><a href="" class="RelayControl" data-id="1">Fever Alarm Output</a></li>
                            </ul>
                        </div>
                    </td>
                    <th>C°/F°</th>
                    <td>
                        <div class="btn-group">
                            <button id="Unit" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="0">°C
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="Unit" data-id="0">°C</a></li>
                                <li><a href="" class="Unit" data-id="1">°F</a></li>
                            </ul>
                        </div>
                    </td>

                    <th>Fever Temperature(°C)</th>
                    <td>
                        <input id="FeverTemperature" type="text" name="FeverTemperature" maxlength="6" value="37.3" />
                    </td>
                </tr>

                <tr>

                    <th>Fever Alarm</th>
                    <td>
                        <div class="btn-group">
                            <button id="FeverAlarm" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Enable
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="FeverAlarm" data-id="0">Off</a></li>
                                <li><a href="" class="FeverAlarm" data-id="1">Enable</a></li>
                            </ul>
                        </div>
                    </td>
                    <th>Access the Door with Temperature</th>
                    <td>
                        <div class="btn-group">
                            <button id="AccessDoor" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Enable
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="AccessDoor" data-id="0">Off</a></li>
                                <li><a href="" class="AccessDoor" data-id="1">Enable</a></li>
                            </ul>
                        </div>
                    </td>


                    <th>Low Temperature to open Door</th>
                    <td>
                        <div class="btn-group">
                            <button id="LowTemperature" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-id="1">Enable
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="" class="LowTemperature" data-id="0">Off</a></li>
                                <li><a href="" class="LowTemperature" data-id="1">Enable</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>


                <tr>
                    <td colspan="6"><button id="btnPreventionSetup" type="button" class="btn btn-primary">Prevention Setup</button></td>
                </tr>
            </table>
        </div>



        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#records" aria-controls="home" role="tab"
                                                          data-toggle="tab">Records</a></li>
                </li>
                <li role="presentation" class=""><a href="#employees" aria-controls="home" role="tab"
                                                    data-toggle="tab">Employees</a></li>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <input type="hidden" id="device_id" value="<?php echo $device['id']; ?>"/>
                <div role="tabpanel" class="tab-pane active" id="records" style="padding: 10px;">
                    <div>
                        <button id="btnDownloadAllRecords" type="button" class="btn btn-primary">Download All Records
                        </button>

                        <button id="btnDownloadNewTemperatureRecords" type="button" class="btn btn-primary">Download New Temperature Records
                        </button>

                        <div style="display: none;">
                            <form id="formImportBackFile"
                                  action="command.php?id=<?php echo $device['id']; ?>&command=importBackFile"
                                  method="post" enctype="multipart/form-data" >
                                <input type="file" name="file"/>
                            </form>
                        </div>
                        <button id="btnImportBackFile" type="button" class="btn btn-primary">Import Backup File
                        </button>
                    </div>
                    <div id="RecordsList">
                        <?php require dirname(__FILE__) . '/common/recordlist.php';?>
                    </div>
                    <div id="TemperatureRecordsList">
                        <?php require dirname(__FILE__) . '/common/temperaturerecordlist.php';?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="employees" style="padding: 10px;">
                    <div>
                        <button id="btnDownloadAllEmployees" type="button" class="btn btn-primary">Download All Employee
                        </button>
                        <button id="btnDownloadAllTemplates" type="button" class="btn btn-primary">Download All
                            Templates
                        </button>
                        <button id="btnClearAllEmployees" type="button" class="btn btn-primary">Clear All Employee &
                            Templates
                        </button>
                        <button id="btnUploadAllEmployees" type="button" class="btn btn-primary">Upload All Employee
                        </button>
                        <button id="btnUploadAllTemplates" type="button" class="btn btn-primary">Upload All Templates
                        </button>
                    </div>
                    <div id="EmployeesList">
                        <?php require dirname(__FILE__) . '/common/employeelist.php';?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.intervalDeviceStatus();
    jQuery(function () {
        jQuery('#btnGetRecordUserFPCount').click(function(e){
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'getRecordUserFPCount'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery.get('ajax.php', {
                            method: 'get_record_user_fp_count',
                            id: device_id
                        }, function(result){
                            jQuery('.userCount').text(result.user);
                            jQuery('.fpCount').text(result.fp);
                            jQuery('.recordCount').text(result.record);
                            jQuery('.lasttimeCount').text(result.lasttime);
                            _this.button('reset');
                        }, 'json');
                    });
                }
            }, 'json');
        });
        jQuery('#btnSetAdminPassWord').click(function(e){
            var _this = jQuery(this);
            var device_id = jQuery('#device_id').val();
            var password = jQuery('input[name="superadminpassword"]').val();
            if(password == ''){
                alert('Please enter the password');
                return false;
            }
            _this.button('loading');
            jQuery.post('command.php', {id: device_id, command: 'setSuperAdminPassword', password: password}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        alert('Success');
                        _this.button('reset');
                    });
                }
            }, 'json');
        });
        jQuery('body').on('click', '.btnEnrollCard', function(e){
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            var idd = _this.attr('data-idd');
            jQuery.post('command.php', {id: device_id, command: 'enrollCard', idd: idd}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function (code) {
                        if(code == '10001'){
                            alert('Timeout');
                            _this.button('reset');
                        }else{
                            jQuery.get('ajax.php', {
                                method: 'get_employee',
                                idd: idd
                            }, function(result){
                                _this.parent().find('font').text(result.cardid);
                                _this.button('reset');
                            }, 'json');
                        }
                    });
                }
            }, 'json');
        });
        jQuery('body').on('click', '.btnEnrollFinger a', function(e){
            e.preventDefault();
            var _this = jQuery(this);
            _this.parents('.btn-group').find('button').button('loading');
            var device_id = jQuery('#device_id').val();
            var idd = _this.attr('data-idd');
            var temp_id = _this.attr('data-sign');
            jQuery.post('command.php', {id: device_id, command: 'enrollFinger', idd: idd, temp_id:temp_id}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery.get('ajax.php', {
                            method: 'get_employee',
                            idd: idd
                        }, function(result){
                            _this.append('&nbsp;Registered');
                            _this.parents('.btn-group').find('button').button('reset');
                        }, 'json');
                    });
                }
            }, 'json');
            return false;
        });
        jQuery('#btnDownloadAllRecords').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'downloadAllRecords'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#RecordsList').load('common/recordlist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });


        jQuery('#btnDownloadNewTemperatureRecords').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'downloadNewTemperatureRecords'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#TemperatureRecordsList').load('common/temperaturerecordlist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });

        jQuery('#btnDownloadAllEmployees').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'downloadAllEmployee'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#EmployeesList').load('common/employeelist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });


        jQuery('#btnOpendoor').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'opendoor'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    console.log('opendoor success');
                    app.checkCommandStatus(result.data.id).done(function () {
                        _this.button('reset');
                    });
                }
            }, 'json');

        });

        jQuery('#btnDownloadAllTemplates').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'downloadAllTemplate'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#EmployeesList').load('common/employeelist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });

        jQuery('#btnClearAllEmployees').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'clearAllEmployees'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#EmployeesList').load('common/employeelist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });

        jQuery('#btnUploadAllEmployees').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'uploadAllEmployees'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#EmployeesList').load('common/employeelist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });

        jQuery('#btnUploadAllTemplates').click(function (e) {
            var _this = jQuery(this);
            _this.button('loading');
            var device_id = jQuery('#device_id').val();
            jQuery.post('command.php', {id: device_id, command: 'uploadAllTemplates'}, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        jQuery('#EmployeesList').load('common/employeelist.php', {id: device_id});
                        _this.button('reset');
                    });
                }
            }, 'json');
        });

        jQuery('#btnImportBackFile').click(function (e) {
            var form = jQuery('#formImportBackFile');
            jQuery('input:file', form).trigger('click');
        });
        jQuery('#formImportBackFile input:file').change(function () {
            var form = jQuery('#formImportBackFile');
            form.submit();
        });

        jQuery('.MaskDetection').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var MaskDetection= jQuery("#MaskDetection");
            MaskDetection.html(_this.text());
            MaskDetection.data("id", id);
        });

        jQuery('.MaskAlarm').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var MaskAlarm= jQuery("#MaskAlarm");
            MaskAlarm.html(_this.text());
            MaskAlarm.data("id", id);
        });

        jQuery('.WorkMode').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var WorkMode= jQuery("#WorkMode");
            WorkMode.html(_this.text());
            WorkMode.data("id", id);
        });

        jQuery('.RelayControl').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var RelayControl= jQuery("#RelayControl");
            RelayControl.html(_this.text());
            RelayControl.data("id", id);
        });

        jQuery('.Unit').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var Unit= jQuery("#Unit");
            Unit.html(_this.text());
            Unit.data("id", id);
        });

        jQuery('.FeverAlarm').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var FeverAlarm= jQuery("#FeverAlarm");
            FeverAlarm.html(_this.text());
            FeverAlarm.data("id", id);
        });

        jQuery('.AccessDoor').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var AccessDoor= jQuery("#AccessDoor");
            AccessDoor.html(_this.text());
            AccessDoor.data("id", id);
        });

        jQuery('.LowTemperature').click(function(e){
            e.preventDefault();
            var _this = jQuery(this);
            var id = _this.data("id");

            var LowTemperature= jQuery("#LowTemperature");
            LowTemperature.html(_this.text());
            LowTemperature.data("id", id);
        });

        jQuery('#btnPreventionSetup').click(function(e){
            var MaskDetection =jQuery("#MaskDetection").data("id");
            var MaskAlarm =jQuery("#MaskAlarm").data("id");

            var WorkMode =jQuery("#WorkMode").data("id");
            var RelayControl =jQuery("#RelayControl").data("id");
            var Unit =jQuery("#Unit").data("id");
            var FeverTemperature = jQuery('input[name="FeverTemperature"]').val();
            var FeverAlarm =jQuery("#FeverAlarm").data("id");
            var AccessDoor =jQuery("#AccessDoor").data("id");
            var LowTemperature =jQuery("#LowTemperature").data("id");

            var device_id = jQuery('#device_id').val();

            var _this = jQuery(this);


            _this.button('loading');
            jQuery.post('command.php', {
                id: device_id,
                command: 'setMaskTemperatureConfig',

                mask_detection:MaskDetection,
                mask_alarm:MaskAlarm,
                work_mode:WorkMode,
                relay_output:RelayControl,
                temp_unit:Unit,
                fever_threshold:FeverTemperature,
                fever_alarm:FeverAlarm,
                temp_opendoor:AccessDoor,
                temp_access:LowTemperature,

            }, function (result) {
                if (!result.success) {
                    alert('Error');
                    _this.button('reset');
                } else {
                    app.checkCommandStatus(result.data.id).done(function () {
                        alert('Success');
                        _this.button('reset');
                    });
                }
            }, 'json');

        });


    });
</script>
<?php
require 'common/footer.php';
?>
