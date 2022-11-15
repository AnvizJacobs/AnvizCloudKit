<?php
/**
 * Created by Jacobs<jacobs@anviz.com>.
 * Date: 18-5-10
 * Time: 上午9:52
 * File Name: recordlist.php
 */
?>
<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/../../database.php');

$id = $_REQUEST['id'];
if (empty($id)) {
    die('error!');
}
?>
<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>idd</th>
        <th>Name</th>
        <th>password</th>
        <th>Card</th>
        <th>Group</th>
        <th>Finger Signed</th>
        <th>Face Signed</th>
        <th>Is Admin</th>
    </tr>
    <?php
    $sql = 'SELECT * FROM employee ORDER BY idd ASC';
    $result = $db->query($sql);
    if($db->num_rows($result) > 0):
        $rowNumber = 1;
        while($row = $db->fetch_array($result)):
            ?>
            <tr>
                <td><?php echo $rowNumber;?></td>
                <td><?php echo $row['idd'];?></td>
                <td><?php echo $row['name'];?></td>
                <td><?php echo $row['passd'];?></td>
                <td><font><?php echo $row['cardid'];?></font>&nbsp;<button type="button" class="btnEnrollCard btn btn-primary btn-xs" data-idd="<?php echo $row['idd'];?>">Enroll Card</button></td>
                <td><?php echo $row['group_id'];?></td>
                <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Enroll Finger <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu btnEnrollFinger">
                        <?php for($i=0; $i<10; $i++):?>
                            <li><a href="#" data-idd="<?php echo $row['idd'];?>" data-sign='<?php echo $i;?>'>
                            <?php echo $i<5?'R':'L';?>&nbsp;
                            <?php echo intval($i%5)+1;?>&nbsp;
                            <?php if(pow(2, $i) & $row['fingersign']):?>Registered<?php endif;?>
                            </a></li>
                        <?php endfor;?>
                      </ul>
                    </div>
                </td>
                <td>
                    <button type="button" class="btnEnrollFace btn btn-primary btn-xs" data-idd="<?php echo $row['idd'];?>">Enroll Face</button>
                    <button type="button" class="btnDownloadFace btn btn-primary btn-xs" data-idd="<?php echo $row['idd'];?>">Download Face</button>
                </td>
                <td><?php echo $row['is_admin'];?></td>
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
