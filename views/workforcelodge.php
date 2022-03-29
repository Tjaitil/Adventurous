            workforcelodge.css|workforcelodge.js|
            <h3 class="page_title">Workforce Lodge</h3>
            <div id="workers">
                <div id="workers-overview">
                    <div id="farmer_workers">
                        <h4> Farmer workforce </h4>
                        <p>Current efficiency level: <?php echo $this->data['farmer_workers']['efficiency_level'];?></p>
                        <table>
                            <tr>
                                <td> Total workforce </td>
                                <td><?php echo $this->data['farmer_workers']['workforce_total'];?></td>
                            </tr>
                            <tr>
                                <td> Towhar workforce </td>
                                <td><?php echo $this->data['farmer_workers']['towhar_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Krasnur workforce </td>
                                <td><?php echo $this->data['farmer_workers']['krasnur_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Available workforce </td>
                                <td>= <?php echo $this->data['farmer_workers']['avail_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Max farmer workers </td>
                                <td><?php echo $this->data['workforce_cap']['farmer'];?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="miner_workers">
                        <h4> Miner workforce </h4>
                        <p> Current efficiency level: <?php echo $this->data['miner_workers']['efficiency_level'];?></p>
                        <table>
                            <tr>
                                <td> Total workforce </td>
                                <td><?php echo $this->data['miner_workers']['workforce_total'];?></td>
                            </tr>
                            <tr>
                                <td> Golbak workforce </td>
                                <td><?php echo $this->data['miner_workers']['golbak_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Snerpiir workforce </td>
                                <td><?php echo $this->data['miner_workers']['snerpiir_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Available workforce </td>
                                <td>= <?php echo $this->data['miner_workers']['avail_workforce'];?></td>
                            </tr>
                            <tr>
                                <td> Max miner workers </td>
                                <td><?php echo $this->data['workforce_cap']['miner'];?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                    <div> 
                        <?php
                            $new_farm_workers = $this->data['workforce_cap']['farmer'] - 
                            $this->data['farmer_workers']['workforce_total'];
                            $new_mine_workers = $this->data['workforce_cap']['miner'] - 
                            $this->data['miner_workers']['workforce_total'];
                            if($new_farm_workers > 0):?>
                                <p class="color:green"> You can recrute more miners </p>
                                <?php endif;
                            if($new_mine_workers > 0):?>
                                <p class="color:green"> You can recrute more farmers </p>
                                <?php endif;?>
                                <?php if($new_farm_workers !== 0 && $new_mine_workers !== 0): ?>
                                    <p> Go to tavern to recrute more workers </p>
                                    <?php else:?> 
                                        <p> Level up farmer and miner to recrute more workers </p>
                                        <?php endif;?>
                                        <p> Go to citycentre to upgrade workers efficieny level</p>
                    </div>
            </div>