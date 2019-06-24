jQuery.Class('app', {
    DeviceStatusInterval: null,
    intervalDeviceStatus: function(params) {
        if (params == 'stop') {
            if (app.DeviceStatusInterval) clearInterval(app.DeviceStatusInterval);
        } else {
            app.DeviceStatusInterval = setInterval(function() {
                jQuery.get('ajax.php', function(result) {
                    jQuery.each(result.device, function(id, row) {
                        jQuery('.is_login_' + row['id']).text(row.is_login);
                        jQuery('.lasttime_' + row['id']).text(row.lasttime);
                    });
                }, 'json');
            }, 10000);
        }
    },
    checkCommandStatus: function(id) {
        var dtd = jQuery.Deferred();
        var func = function() {
            jQuery.get('ajax.php', {
                method: 'command_status',
                id: id
            }, function(result) {
                if (result.success) {
                    var code = 0;
                    if (typeof result.code != 'undefined') code = result.code;
                    dtd.resolve(code); 
                } else {
                    setTimeout(function() {
                        func();
                    }, 2000);
                }
            }, 'json');
        };
        func();
        return dtd.promise();
    }
}, {});