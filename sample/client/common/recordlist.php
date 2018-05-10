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
    </tr>
    <?php
    $sql = 'SELECT * FROM records WHERE device_id="'.$id.'" ORDER BY checktime DESC';
    $result = mysql_query($sql);
    if(mysql_num_rows($result) > 0):
        $rowNumber = 1;
        while($row = mysql_fetch_array($result)):
            ?>
            <tr>
                <td><?php echo $rowNumber;?></td>
                <td><?php echo $row['idd'];?></td>
                <td><?php echo date('m/d/Y H:i:s', $row['checktime']);?></td>
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
