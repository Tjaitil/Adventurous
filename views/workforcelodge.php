            workforcelodge.css||
            <h3 id="page_title">Workforce Lodge</h3></br>
            <div id="farmer_workers">
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
                </table>
            </div>
            <div id="miner_workers">
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
                </table>
            </div>
            <a href="/tavern"> Hire more workers </a>
            <a href="/laboratory"> Upgrade workers </a>