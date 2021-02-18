        <table>
            <caption> Countdowns </caption>
            <thead>
                <tr>
                    <td> Profiency </td>
                    <td> What </td>
                </tr>
            </thead>
            <tr>
                <td> Farmer </td>
                <td><p>Towhar: <?php echo $data['farmer'][0];?></p>
                    <p>Cruendo: <?php echo $data['farmer'][1];?></p>
                </td>
            </tr>
            <tr>
                <td> Miner </td>
                <td><p>Golbak: <?php echo $data['miner'][0];?></p>
                    <p>Snerpiir: <?php echo $data['miner'][1];?></p>
                </td>
            </tr>
            <tr>
                <td> Trader </td>
                <td><?php echo 'Assignment: ' , $data['trader'];?></td>
            </tr>
            <tr>
                <td> Warrior </td>
                <td><p><?php echo $data['warrior']['armymission']; ?></p>
                    <p><?php echo 'Warrior(s) finished training: ' , $data['warrior']['finished'];?></p>
                    <p><?php echo 'Warrior(s) training: ' , $data['warrior']['training'];?></p>
                    <p><?php echo 'Warrior(s) on mission: ' , $data['warrior']['mission'];?></p>
                    <p><?php echo 'Warrior(s) idle: ' , $data['warrior']['idle'];?></p>
                </td>
            </tr>
        </table>