<?php foreach($data['warrior_data'] as $key): ?>
        <tr>
            <td><input type="checkbox" value="<?php echo $key['warrior_id']; ?>" /></td>
            <td><?php echo $key['warrior_id'] ?></td>
            <td><?php echo $key['type'] ?></td>
            <td><?php echo $key['stamina_level'] ?></td>
            <td><?php echo $key['technique_level'] ?></td>
            <td><?php echo $key['precision_level'] ?></td>
            <td><?php echo $key['strength_level'] ?></td>
            <td></td>
        </tr>
<?php endforeach;