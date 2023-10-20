<?php if (intval($this->data['adventure']['adventure_id']) != 0) : ?>
    <div id="people" class="mt-1">
        <figure>
            <img src="<?php echo constant("ROUTE_IMG") . 'farmer icon.png'; ?>" title="farmer" />
            <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['farmer']); ?></figcaption>
        </figure>
        <figure>
            <img src="<?php echo constant("ROUTE_IMG") . 'miner icon.png'; ?>" title="miner" />
            <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['miner']); ?></figcaption>
        </figure>
        <figure>
            <img src="<?php echo constant("ROUTE_IMG") . 'trader icon.png'; ?>" title="trader" />
            <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['trader']); ?></figcaption>
        </figure>
        <figure>
            <img src="<?php echo constant("ROUTE_IMG") . 'warrior icon.png'; ?>" title="warrior" />
            <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['warrior']); ?></figcaption>
        </figure>
    </div>
    <div id="status" class="mt-1">
        <?php get_template('adventure_status', $this->data['adventure']); ?>
    </div>
    <div id="requirements" class="mt-1">
        <table class="middle-align">
            <thead>
                <tr>
                    <td><b>Role</b></td>
                    <td><b>Requirement</b></td>
                    <td><b>Provided</b></td>
                </tr>
            </thead>
            <?php get_template('requirements', $this->data['adventure']['requirements']); ?>
        </table>
    </div>
<?php else : ?>
    <span> No current adventure! </span>
<?php endif; ?>
<div id="adventure-invites">
    <?php get_template(
        'adventure_requests',
        array("requests" => $this->data['adventure']['requests'], "invites" => $this->data['adventure']['invites']),
        false,
        array("site" => "adventures")
    ); ?>
</div>