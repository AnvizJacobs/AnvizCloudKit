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
        <th>Check Time</th>
        <th>Mask</th>
        <th>Temperature(°C)</th>

    </tr>
    <?php
    $sql = 'SELECT * FROM temperature_records WHERE device_id="'.$id.'" ORDER BY checktime DESC';
    $result = $db->query($sql);
    if($db->num_rows($result) > 0):
        $rowNumber = 1;
        while($row = $db->fetch_array($result)):
            ?>
            <tr>
                <td><?php echo $rowNumber;?></td>
                <td><?php echo $row['idd'];?></td>
                <td><?php echo date('m/d/Y H:i:s', $row['checktime']);?></td>
                <td><?php echo $row['mask'];?></td>
                <td><?php echo $row['temperature'];?></td>
            </tr>
            <?php
            $rowNumber++;
        endwhile;
    else:

        ?>
        <tr>
            <td colspan="6">There has no temperature record.</td>
        </tr>
    <?php
    endif;
    ?>
    </thead>
</table>
