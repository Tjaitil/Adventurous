<?php foreach($data as $key): ?>
    <li><?php echo '[' . date("H:i:s", strtotime($key['clock'])) . ']  ' . ucfirst($key['username']) . ': ' , $key['message'];?></li>    
<?php endforeach;?>