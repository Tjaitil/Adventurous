<?php foreach($data as $key): ?>
        <tr class="message_row">
            <td><input type="checkbox" /></td>
            <td><a onclick="showMessage(<?php echo $key['id'];?>, this)"><?php echo $key['title']; ?></a></td>
            <td><?php echo ucfirst($key['receiver']); ?></td>
            <td><?php echo $key['date']; ?></td>
            <td><img src="<?php echo $key['message_read'];?>.jpg"/></td>
        </tr>
<?php endforeach;?>