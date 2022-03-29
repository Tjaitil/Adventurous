<?php foreach($data as $key): ?>
    <tr>
        <td <?php echo (strpos($key, 'ERROR:') ? 'class="error_log"' : "");?>><?php echo $key;?></td>
    </tr>
<?php endforeach;?>