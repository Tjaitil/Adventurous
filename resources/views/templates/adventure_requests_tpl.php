<!-- Create two tbodys, one for invites and on for request -->
<table>
<caption> Adventure requests and invites </caption>
    <thead>
        <tr>
            <td> Sender </td>
            <td> Method </td>
            <td> Role </td>
            <td> Date </td>
            <td> </td>
        </tr>
    </thead>
    <tbody>
        <?php
        if(count($data['requests']) === 0): ?>
            <tr>
                <td colspan="6" align="center"> No requests at the moment!</td>
            </tr>
        <?php else: ?>
        <?php foreach($data['requests'] as $key) : ?>
            <tr>
                <td><?php echo ucfirst($key['sender']); ?></td>
                <td><?php echo $key['method']; ?></td>
                <td><?php echo $key['role']; ?></td>
                <td><?php echo $key['request_date']; ?></td>
                <?php
                if($flag['site'] !== "aside"):?>
                    <td>
                        <button onclick="joinAdventure(<?php echo $key['request_id']; ?>);"> Accept </button>
                        <button onclick="joinAdventure(<?php echo $key['request_id']; ?>);"> Decline </button>
                    </td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tbody>
        <?php if(count($data['invites']) === 0): ?>
            <tr>
                <td colspan="6" align="center"> No invites at the moment!</td>
            </tr>
        <?php else: ?>
        <?php foreach($data['invites'] as $key) : ?>
            <tr>
                <td><?php echo ucfirst($key['receiver']); ?></td>
                <td><?php echo $key['method']; ?></td>
                <td><?php echo $key['role']; ?></td>
                <td><?php echo $key['request_date']; ?></td>
                <?php
                if($flag['site'] !== "aside"):?>
                    <td>
                        <button onclick="showAdventure(<?php echo $key['adventure_id']; ?>);"> Show Info </button>
                        <button onclick="joinAdventure(<?php echo $key['request_id']; ?>);"> Accept </button>
                    </td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6"><button class="previous">
                    < Prev </button><button class="next"> Next > </button></td>
        </tr>
    </tfoot>
</table>