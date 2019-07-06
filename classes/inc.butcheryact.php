<?php
class butcheryact extends getButcheryData {
    public $user;
    //Error messages
    public $typeErr;
    public $quantErr;
    public $workforceErr;
    public $selectErr;
    // Variables from form
    public $butchType;
    public $butchQuant;
    public $butchWorkforce;
    public $butchAction;
    //
    public $butchCountdown;
    public $butchXP;
    public $butchTime;
    
    function checkData () {
        if (empty($_POST['type'])) {
            $this->typeErr = "Please select a type of animal";
        }
        if (empty($_POST['quant'])) {
            $this->quantErr = "Please select a quantity";
        }
        if (empty($_POST['workforce']) || $_POST['workforce'] === 0) {
            $this->workforceErr = "Please select quantity of workers";
        }
        /* else if ($_POST['workforce'] > /*$this->availworkforce) {
            $this->workforceErr = "You dont have that many workers available";
        } */
        if (empty($_POST['action'])) {
            $this->selectErr = "Please select an action";
        }
        else {
            $this->butchType = $_POST['type'];
            $this->butchQuant = $_POST['quant'];
            $this->butchWorkforce = $_POST['workforce'];
            $this->butchAction = $_POST['action'];
            $this->updateData();
        }
    }
    
    function updateData($sesUser) {
        if ($this->butchAction === "breed") {
            $stmt = $this->connect()->prepare("SELECT time, experience FROM breeddata WHERE Type=?");
            $stmt->bind_param('s', $this->butchType);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                while ($row = $stmt->fetch_assoc()) {
                    $this->butchTime = $row['time'];
                    $this->butchXP = $row ['experience'];
                }
            }
            $date = date("Y-m-d H:i:s");
            $datetime = new DateTime($date);
            $timeToAdd = $this->butchQuant * $this->butchTime;
            $datetime->modify("+{$timeToAdd} seconds");
            $this->butchCountdown = date_format($datetime, "Y-m-d H:i:s");
            $stmt = $this->mysqli->prepare("UPDATE butchery SET grow_type=?, grow_quant=?, grow_countdown=? WHERE username=?");
            $stmt->bind_param('ssss', $this->butchType, $this->butchQuant, $this->butchCountdown, $this->user);
            if($stmt->execute()) {
                header("location: butchery.php");
            }
            else {
                echo "<script>alert('Something went wrong')</script>";
            }
            $this->connect()->close();
        }
        else if ($this->butchAction === "slaught") {
            $stmt = $this->mysqli->prepare("SELECT time, experience FROM slaughtdata WHERE Type=?");
            $stmt->bind_param('s', $this->butchType);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                while ($row = $stmt->fetch_assoc()) {
                    $this->butchTime = $row['time'];
                    $this->butchXP = $row['experience'];
                }
            }
            $date = date ("Y-m-d H:i:s");
            $datetime = new DateTime($date);
            $timeToAdd = $this->butchQuant * $this->butchTime;
            $datetime->modify("+{$timeToAdd} seconds");
            $stmt = $this->mysqli->prepare("UPDATE butchery SET slaught_type=?, slaught_quant=?, slaught_countdown=? WHERE username=?");
            $stmt->bind_param('ssss', $this->butchType, $this->butchQuant, $this->butchCountdown, $this->user);
            if ($stmt->execute()) {
                header("location: butchery.php");
            }
            else {
                echo "<script>alert('Something went wrong')</script>";
            }
            
            $this->connect()->close();
        }
    }
}
?>