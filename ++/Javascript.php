<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title> Javascript </title>
    <style>
        #table_test tr td {
            border:1px solid black;
            border-collapse: collapse;
            width:100px;
            text-align: center;
        }
    </style>
</head>
<body style="margin:50px 0px 0px 300px;">
    <script src="java.js"></script>
        <p style="height:100px; width:100px;" id="demo">
        </p>
        <button onclick="fill ();">
        Click to fill box
        </button>
        
        
        
        
        
        <a href="troop.html"/> Troop calculator </a>
    <div>
        <input type="text" id="name_1" />        
        <button onclick="setCookie();"> Check cookie </button>
        <input type="text" id="name_2"/>
        <button onclick="SetyetCookie();"> Check cookie </button>
        <input type="text" id="name_3"/>
        <button onclick="checkCookie();"> Get cookies</button>
        <p id="demo"> No cookie </p>
    </div>
    
    <div>
        Check city: <input id="input_1" placeholder="Enter city"/>
        Hello 2: <input id="input_2" />
        Hello 3: <input id="input_3" />
        Hello 4: <input id="input_4" />
        <button onclick="check_array();"> Check Array </button>
    </div>
    </br>
    </br>
    </br>
    </br>
      <div>
        <table id="table_test">
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
            </tr>
             <tr>
                <td>1.1</td>
                <td>1.2</td>
                <td>1.3</td>
                <td>1.4</td>
                <td>1.5</td>
                <td>1.6</td>
            </tr>
        </table>
        <button onclick="change_table();" /> Press to change </button>
      </div>
      <script src="java.js"></script>
</body>
</html>

