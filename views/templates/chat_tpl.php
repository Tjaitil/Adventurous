<?php
    foreach($data as $key): ?>
    <li><?php echo '[' . $key['clock'] . '] ' . ucfirst($key['username']) . ': ' , $key['message']; ?></li>    
<?php endforeach;?>