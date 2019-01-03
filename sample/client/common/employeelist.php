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
                <td><?php echo $row['cardid'];?></td>
                <td><?php echo $row['group_id'];?></td>
                <td><?php echo $row['fingersign'];?></td>
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
