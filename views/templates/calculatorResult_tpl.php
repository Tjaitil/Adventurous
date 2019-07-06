<span><?php echo $data['result'];?></span>
<div id="">
    <?php for($i = 0; $i < count($data['battle_progress']); $i++) {
        echo $data['battle_progress'][$i] . "</br>";
    }
    ?>
</div>
<table>
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
        <td><?php echo $data['warrior_wounded']; ?></td>
        <td><?php echo $data['daqloon_wounded']; ?></td>
    </tr>
    <tr>
        <td> Combo attack: </td>
        <td><?php echo $data['warrior_combo']; ?></td>
        <td><?php echo $data['daqloon_combo']; ?></td>
    </tr>
</table>