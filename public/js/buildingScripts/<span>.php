                <span>
                    <span class="item_buy_price"><?php echo number_format($price_info["buy_price"], 0, ',', '.'); ?></span><br>
                    <?php if ($price_info["difference"] !== 0) : ?>
                        <span class="<?php echo $price_info["class"]; ?>"><?php echo '(' . $price_info["difference"] . ')'; ?></span>
                    <?php endif; ?>
                </span>
                <span><?php echo number_format($key['sell_value'], 0, ',', '.'); ?></span>