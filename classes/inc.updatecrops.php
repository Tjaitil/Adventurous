<?php
class updateCrops extends dbh  {
    // Update database when countdown is done
    public $fieldsd;
    public $avaWorkforced;
    public $cropQuantd;
    public $cropWorkforced;
    public $updtFields;
    public $countdownd;
    public $updtCropType;
    public $updtCropquant;
    public $updtCropWorkforce;
    public $updtAvaWorkforce;
    public $user;
    function __construct ($session) {
         $this->user = $session;
         $sql = "SELECT Crop_workforce, Crop_Countdown FROM Farmer WHERE Username='$this->user'";
         $result = $this->connect()->query($sql);
         $numrows = $result->num_rows;
         if ($numrows > 0) {
            while ($row = $result->fetch_assoc()) {
                $this->cropWorkforced = ['Crop_workforce'];
                $this->countdownd = ['countdownd'];
            }
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            echo date_format($newDate, "Y-m-d H:i:s");
            $newdate2 = new DateTime($this->countdownd);
            echo date_format($newdate2, "Y-m-d H:i:s");
            /* if ($this->cropWorkforced > 0 && $date > $this->countdownd) {
                $this->getdata();
            }
            else {
                exit;
                echo "Records already updated";
            } */
         }
    }
    function getdata() {
        $this->user = $session;
        $sql = "SELECT Fields, Crop_quant, Crop_workforce, Avail_workforce FROM Farmer WHERE Username='$this->user'";
        $result = $this->connect()->query($sql);
        $numrows = $result->num_rows;
        if($numrows > 0) {
            while ($row = $result->fetch_assoc()) {
                $this->fieldsd = $row['Fields'];
                $this->cropQuantd = $row['Crop_quant'];
                $this->cropWorkforced = $row['Crop_workforce'];
                $this->avaWorkforced = $row['Avail_workforce'];
            }
            $this->update();
        } 
    }
    
    function update () {
        $this->updtFields = $this->fieldsd + $this->cropQuantd;
        $this->updtCropType = "None";
        $this->updtCropquant = 0;
        $this->updtAvaWorkforce = $this->cropWorkforced + $this->avaWorkforced;
        $this->updtCropWorkforce = "0";
        $sql = "UPDATE Farmer SET Fields='$this->updtFields', Crop_type='$this->updtCropType', Crop_quant='$this->updtCropquant', Crop_workforce='$this->updtCropWorkforce',
        Avail_workforce='$this->updtAvaWorkforce' WHERE Username='$this->user'";
        $result= $this->connect()->query($sql);
        if($this->connect()->query($sql) === TRUE) {
            echo "Updating sucessfull";
        }
        else {
            echo "Something went wrong";
        }
    }
}

?>