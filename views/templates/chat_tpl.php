<?php foreach($data as $key): ?>
    <li><?php echo '[' . date("H:i:s", strtotime($key['time'])) . ']  ' . ucfirst($key['username']) . ': ' , $key['message'];?></li>    
<?php endforeach;?>