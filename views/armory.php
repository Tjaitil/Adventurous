            armory.css|armory.js|
            <h3 class="page_title"> Armory </h3>
            <button> Back to army camp</button>
            <div id="warriors">
                <div id="put_on">
                    <div id="selected">
                    </div>
                    <select id="select_warrior">
                        <option></option>
                        <?php foreach($this->data['warrior_armory'] as $key): ?>
                        <option><?php echo $key['warrior_id'];?></option>
                        <?php endforeach;?>
                    </select>
                    <select id="type">
                        <option value="right"> Right hand </option>
                        <option value="left"> Left hand </option>
                    </select>
                    <div id="ranged_alt">
                        <label for="amount"> Amount of arrows/knives</label>
                        <input name="amount" type="number" min="1" />
                    </div>
                    <button onclick="wearArmor();"> Put on </button>
                </div>
                <?php get_template('armory', $this->data['warrior_armory'], true) ;?>
            </div>
            </br>