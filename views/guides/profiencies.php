<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Profiencies </h3>
<div id="content_table">
    <ol>
        <?php $contents = array("about", "advantages");
        for($i = 0; $i < count($contents); $i++): ?>
            <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
        <?php endfor;?>
    </ol>
</div>
<h5 class="p_header" id="about"> About </h5>
<p> Profiencies is a core part of adventurous. At the start of the game you wil need to pick one profiency from five.
    The profiencies you can chose from is farmer, miner, trader and warrior.
    The profiency can be changed at any point in game in
    <a href="/gameguide/citycentre"> City Centre </a></p>
    Note that when you change your profiency all actions which require over level 30 in the profiency you changed from will be unavaiable.
    In other words this means you cannot for example have gear on your warriors that is above level 30.
    
<h5 class="p_header" id="advantages"> Advantages </h5>
<p> First advantage of profiency is that you can level up over level 30. When the other skills reached level 30 you will not be gaining more
    experience.Each of the profiencies gives you certain advantages. Below is a list over the different advantages.
    Note also that each of profiencies
get different rewards from adventures!</p>
<table>
    <thead>
        <tr>
            <td>Profiency</td>
            <td>Advantages</td>
            <td>Adventure reward</td>
        </tr>
    </thead>
    <tr>
        <td>Farmer</td>
        <td>Bakery prices down 75 %</td>
        <td></td>
    </tr>
    <tr>
        <td>Miner</td>
        <td>Blacksmith prices down 10%, and kepys prices down 30 %</td>
        <td>Get wujkin ore from adventures</td>
    </tr>
    <tr>
        <td>Trader</td>
        <td>Get goods from trader assignment instead of gold</td>
        <td></td>
    </tr>
    <tr>
        <td>Warrior</td>
        <td>Access to wujkin armor</td>
        <td>Chance to get weapon tokens from adventures</td>
    </tr>
</table>