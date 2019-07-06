<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $title ?>.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require('views/header.php'); ?>
        </header>
        <section>
            <?php var_dump($this->data);?>
            <div id="actions">
                <button onclick="toggle('inbox');">Inbox</button>
                <button onclick="toogle('sent');">Sent</button>
                <button onclick="showWriteMessage();"> New Message: </button>
            </div>
            <div id="write_message">
                <form id="message_form" method="post" action="/messages">
                <label for="name"> Title: </label>
                <input type="text" name="title" /></br>
                <label for="receiver"> Receiver: </label>
                <input id="receiver" type="text" name="receiver" onfocusout="setTimeout(userCheck, 1000)" /></br>
                <textarea id="the_message" name="message" placeholder="Enter message here:"
                          style="background-image: url(<?php echo constant("ROUTE_IMG") . 'background.png';?>)">
                    
                </textarea></br>
                <button name="send"> Send message </button>
                </form>
            </div>
            <table id="inbox">
                <tr>
                    <td></td>
                    <td> Title </td>
                    <td> From </td>
                    <td> Date </td>
                    <td> Read </td>
                </tr>
                    <?php foreach($this->data['inbox'] as $key): ?>
                            <tr id="message_row">
                                <td><input type="checkbox" /></td>
                                <td><a onclick="showMessage(<?php echo $key['id'];?>, this)" ><?php echo $key['title']; ?></a></td>
                                <td><?php echo $key['sender']; ?></td>
                                <td><?php echo $key['date']; ?></td>
                                <td><img src="<?php echo $key['message_read'];?>.jpg"/></td>
                            </tr>
                    <?php endforeach;
                    ?>
            </table>
            <table id="sent">
                <tr>
                    <td></td>
                    <td> Title </td>
                    <td> From </td>
                    <td> Date </td>
                    <td> Read </td>
                </tr>
                    <?php foreach($this->data['sent'] as $key): ?>
                            <tr id="message_row">
                                <td><input type="checkbox" /></td>
                                <td><a onclick="showMessage(<?php echo $key['id'];?>, this)"><?php echo $key['title']; ?></a></td>
                                <td><?php echo $key['sender']; ?></td>
                                <td><?php echo $key['date']; ?></td>
                                <td><img src="<?php echo $key['message_read'];?>.jpg"/></td>
                            </tr>
                    <?php endforeach;
                    ?>
            </table>
            <div id="message">
                <table id="message_info">
                    <tr>
                        <td> Title: </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> From: </td>
                        <td></td>
                    </tr>
                </table>
                <button onclick="toggle('inbox');"> Back to messages:</button>
                <div id="message_content">
                    
                </div>
                <button onclick="answer();"> Answer </button>
            </div>
            <div id="area"><p> Area</p></div>
            <script src="<?php echo constant('ROUTE_JS') . $name . ".js"; ?>"></script>
        </section>
        <aside>
            <?php require('views/aside.php'); ?>
        </aside>
    </body>
</html>
