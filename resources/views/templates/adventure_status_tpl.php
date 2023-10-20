<?php
    $test1 = in_array('none', array($data['info']['farmer'],
            $data['info']['miner'],
            $data['info']['trader'],
            $data['info']['warrior']));
    $test2 = in_array(0, $data['requirements']);
    $test3 = $data['info']['adventure_status'] == 0;
    if($test1 == true && $test3 !== false): ?>
    <p> Adventure status: more players needed </p>
    <?php elseif($test2 === false && $test3 == true): ?>
    <p> Adventure status: awaiting providing </p>
    <?php elseif($test1 === false && $test3 == true): ?>
    <p> Adventure status: ready to start! </p>
    <?php elseif($test1 === false && $test2 == false && $test1 === false): ?>
    <p> Adventure status: underway! </p>
<?php endif;?>