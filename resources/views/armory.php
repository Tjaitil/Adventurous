            armory.css|armory.js|
            <?php

            use App\libs\TemplateFetcher;
            ?>
            <h1 class="page_title"> Armory </h1>
            <button> Back to army camp</button>
            <div id="warriors">
                <div class="help">
                    <p> Select an item from inventory to put on warrior or click one of the items worn by warriors to unequip.</p>
                </div>
                <div id="put_on" class="mb-1">
                    <?php

                    echo TemplateFetcher::loadTemplate("select_item", [
                        "show_amount_input" => true,
                        "selected_amount_label" => "Amount of arrows/knives"
                    ]);
                    ?>
                    <label for="warrior_id">Select warrior</label>
                    <select name="warrrior_id" id="select_warrior">
                        <option selected disabled hidden></option>
                        <?php foreach ($this->data['warrior_armory'] as $key) : ?>
                            <option><?php echo $key['warrior_id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="type">
                        <option value="right"> Right hand </option>
                        <option value="left"> Left hand </option>
                    </select>
                    <div id="ranged_alt">
                        <label for="amount"> Amount of arrows/knives</label>
                        <input name="amount" type="number" min="1" />
                    </div>
                    <button id="put_on_button"> Put on </button>
                </div>
                <div id="warrior_container">
                    <?php
                    echo TemplateFetcher::loadTemplate("armory", $this->data['warrior_armory']);
                    ?>
                </div>
            </div>
            </br>