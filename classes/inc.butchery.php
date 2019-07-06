<?php
class getButcheryData extends dbh{
    public $user;
    public $farmlevelD;
    public $chickensD;
    public $pigsD;
    public $cowsD;
    public $growTypeD;
    public $slaughtTypeD;
    public $growCountdownD;
    public $slaughtountdownD;
    
    function fetchData ($sesUser) {
        $this->user = $sesUser;
        $sql = "SELECT farmlevel FROM Farmer WHERE Username='$this->user'";
        $result = $this->connect()->query($sql);
        $numrows = $result->num_rows;
        if ($numrows > 0) {
            while ($row = $result->fetch_assoc()) {
                $this->farmlevelD = $row['Farmlevel'];
            }
            if ($this->farmlevelD < 15) {
                echo "<script> alert('You are too low level to do any actions');</script>";
            }
            else if ($this->farmlevelD > 15) {
                $this->getRestData();
            }
        }
    }
    function getRestData () {
        $sql = "SELECT chickens, pigs, cows, grow_type, grow_quant, grow_countdown, slaught_type, slaught_quant, slaught_countdown FROM butchery WHERE username='$this->user";
        $result = $this->connect()->query($sql);
        $numrows = $result->num_rows;
        if ($numrows > 0){
            while ($row = $result->fetch_assoc()) {
                $this->chickensD = $row['chickens']; 
                $this->pigsD = $row['pigs'];
                $this->cowsD = $row['cows'];
                $this->growTypeD = $row['grow_type'];
                $this->growCountdownD = $row['grow_countdown'];
                $this->slaughtTypeD = $row['slaught_type'];
                $this->slaughtCountdownD = $row['slaught_countdown'];
            }
            $this->connect()->close();
        }
    }
}
?>