<?php foreach($data as $key): ?>
        <div id="warrior_<?php echo $key['warrior_id'];?>" class="warrior">
            <div class="info">
                <img src="#" />
                <table>
                    <tr>
                        <td> Id: </td>
                        <td><?php echo $key['warrior_id'];?></td>
                    </tr>
                    <tr>
                        <td> Health: </td>
                        <td><?php echo $key['health'];
                        echo ($key['rest'] == '1') ? " (" . calculateHealth($key['rest_start'], $key['health']) . ")" : "";?></td>
                    </tr>
                    <tr>
                        <td> Status: </td>
                        <td><?php echo $key['status'];?></td>
                    </tr>
                </table>
            </div>
            <div class="levels">
                <p class="countdown"></p>
                <ul>
                    <li><p>Technique level <?php echo $key['technique_level'];?></p>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress"><span class="progress_value1">1</span>
                            &nbsp/&nbsp<span class="progress_value2">1000</span>
                            </div>
                        </div>
                    </li>
                    <li><p>Stamina level <?php echo $key['stamina_level'];?></p>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress"><span class="progress_value1">1</span>
                            &nbsp/&nbsp<span class="progress_value2">1000</span>
                            </div>
                        </div>
                    </li>
                    <li><p>Precision level <?php echo $key['precision_level'];?></p>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress"><span class="progress_value1">1</span>
                            &nbsp/&nbsp<span class="progress_value2">1000</span>
                            </div>
                        </div>
                    </li>
                    <li><p>Strength level <?php echo $key['technique_level'];?></p>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress"><span class="progress_value1">1</span>
                            &nbsp/&nbsp<span class="progress_value2">1000</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
    </div>
<?php endforeach;?>