<?php foreach($data as $key): ?>
    <?php echo htmlspecialchars('[' . date("H:i:s", strtotime($key['clock'])) . ']  ' . ucfirst($key['username']) . ': ' . $key['message'] . '*^%' . $key['id']);?>|
<?php endforeach;?>