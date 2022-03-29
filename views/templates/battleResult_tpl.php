    <table id="battle_stats">
        <caption>Battle result: <?php echo $data['result'];?></caption>
        <thead>
            <tr>
                <td> </td>
                <td> Warrior: </td>
                <td> Daqloon: </td>
            </tr>
        </thead>
        <tr>
            <td> Damage dealt: </td>
            <td><?php echo $data['warrior_damage']; ?></td>
            <td><?php echo $data['daqloon_damage']; ?></td>
        </tr>
        <tr>
            <td> Wounded: </td>
            <td><?php echo $data['stats']['wounded']['warrior']; ?></td>
            <td><?php echo $data['stats']['wounded']['daqloon']; ?></td>
        </tr>
        <tr>
            <td> Blocked attack: </td>
            <td><?php echo $data['stats']['blocked']['warrior']; ?></td>
            <td><?php echo $data['stats']['blocked']['daqloon']; ?></td>
        </tr>
        <tr>
            <td> Missed attack: </td>
            <td><?php echo $data['stats']['missed']['warrior']; ?></td>
            <td><?php echo $data['stats']['missed']['daqloon']; ?></td>
        </tr>
    </table>
    <div id="battle-result">
        <?php foreach($data['battle_progress'] as $key => $inner_array): ?>
            <div class="battle-progress-container">
                <div class="battle-progress-hit"><?php echo "Hit " . $key;?></div>
                <div class="battle-progress-info">
                    <?php $i = 0; foreach($inner_array as $value): ?>
                        <p><?php echo $value;?></p>
                    <?php $i++; endforeach;?>
                </div>            
            </div>
        <?php endforeach;?>
    </div>