    <h3 class="page_title"> Adventure rewards </h3>
    <div id="adventure_report">
        <p><button> << Back to adventures</button></p>
        <div id="adventure_rewards">
            <h3> You have finished an adventure! </h3>
            <p> Rewards: </p>
    <?php foreach($data['rewards'] as $key): ?>
            <div class="item">    
                <figure onclick="show_title(this, false);">
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']);?></figcaption>
                </figure>
                <span class="item_amount"><? echo $key['amount'];?></span>
            </div>
        <?php endforeach; ?>
        <div id="item_tooltip">
            <ul>
                <li></li>
            </ul>
        </div>
        </div>
        <?php if($data['stockpile_insert'] === 'true'): ?>
            <p> NOTE: One or more of your rewards are inserted directly into your stockpile </p>
        <?php endif;?>
            <table id="adventure_stats"s>
                <thead>
                    <tr>
                        <td> Location: </td>
                        <td> Difficulty: </td>
                        <td> Role: </td>
                        <td> Xp Gained </td>
                    </tr>
                </thead>
                <tr>
                    <td><?php echo ucfirst($data['adventure_data']['location']);?></td>
                    <td><?php echo $data['adventure_data']['difficulty'];?></td>
                    <td><?php echo ucfirst($data['adventure_data']['role']);?></td>
                    <td><?php echo $data['adventure_data']['user_xp'];?></td>
                </tr>
            </table>
        <table id="battle_stats">
            <caption>Battle result: <?php echo $data['statistics']['result'];?></caption>
            <thead>
                <tr>
                    <td> </td>
                    <td> Warrior: </td>
                    <td> Daqloon: </td>
                </tr>
            </thead>
            <tr>
                <td> Damage dealt: </td>
                <td><?php echo $data['statistics']['warrior_damage']; ?></td>
                <td><?php echo $data['statistics']['daqloon_damage']; ?></td>
            </tr>
            <tr>
                <td> Wounded: </td>
                <td><?php echo $data['statistics']['warrior_wounded']; ?></td>
                <td><?php echo $data['statistics']['daqloon_wounded']; ?></td>
            </tr>
            <tr>
                <td> Combo attack: </td>
                <td><?php echo $data['statistics']['warrior_combo']; ?></td>
                <td><?php echo $data['statistics']['daqloon_combo']; ?></td>
            </tr>
        </table>
        <?php if(count($data['warrior_xp']) > 0): ?>
            <table id="warriors_levels">
                <thead>
                    <tr>
                        <td> Warrior_id </td>
                        <td> Stamina xp </td>
                        <td> Technique xp </td>
                        <td> Precision xp </td>
                        <td> Strength xp </td>
                    </tr>
                    <?php foreach($data['warrior_xp'] as $key): ?>
                        <tr>
                            <td><?php echo $key['warrior_id'];?></td>
                            <td><?php echo $key['stamina_xp'];?></td>
                            <td><?php echo $key['technique_xp'];?></td>
                            <td><?php echo $key['precision_xp'];?></td>
                            <td><?php echo $key['strength_xp'];?></td>
                        </tr>
                    <?php endforeach;?>
                </thead>
            </table>
        <?php endif;?>
    </div>