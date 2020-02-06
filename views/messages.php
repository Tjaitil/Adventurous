<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php require(constant('ROUTE_VIEW') . '/head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name;?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . '/header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . '/layout.php');?>
            <div id="actions">
                <button> Inbox </button>
                <button> Sent </button>
                <button> Write Message</button>
            </div>
            <div id="write_message">
                <form id="message_form" method="post" action="/messages">
                <label for="name"> Title: </label>
                <input type="text" name="title" /></br>
                <label for="receiver"> Receiver: </label>
                <input id="receiver" type="text" name="receiver" /></br>
                <textarea id="the_message" name="message" placeholder="Enter message here:"
                          style="background-image: url(<?php echo constant("ROUTE_IMG") . 'background.png';?>)">
                    
                </textarea></br>
                <button name="send"> Send message </button>
                </form>
            </div>
            <table id="inbox">
                <thead>
                    <tr>
                        <td></td>
                        <td> Title </td>
                        <td> From </td>
                        <td> Date </td>
                        <td> Read </td>
                    </tr>
                </thead>
                    <?php get_template("messages", $this->data['inbox']);?>
                <tfoot>
                    <tr>
                        <td colspan="5"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                    </tr> 
                </tfoot>
            </table>
            <table id="sent">
                <thead>
                    <tr>
                        <td></td>
                        <td> Title </td>
                        <td> To </td>
                        <td> Date </td>
                        <td> Read </td>
                    </tr>
                </thead>
                    <?php get_template("messages", $this->data['sent']);?>
                <tfoot>
                    <tr>
                        <td colspan="5"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                    </tr> 
                </tfoot>
            </table>
            <div id="message" class="page_content">
                <table id="message_info">
                    <tr>
                        <td> Title: </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> From: </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td> Date </td>
                        <td></td>
                    </tr>
                </table>
                <button> Back to messages </button>
                <div id="message_content">
                    
                </div>
                <button onclick="answer();"> Answer </button>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . ".js"; ?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php');?>
        </aside>
    </body>
</html>
