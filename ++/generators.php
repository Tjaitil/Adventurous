<?php
    $cities = array("towhar", "golbak", "snerpiir", "krasnur", "ter", "pvitul");
    function assignments($count) {
        
        
        $items;
        $amount = array("easy" => "10/30", "medium" => "30/50", "hard" => "70/100");
        
        $difficulties = array("easy", "medium", "hard");
        
        
        
        $assignments = array();
        
        for($i = 0; $i < $count; $i++) {
            $assignment = array();
            $base = $assignment['base'] = array_rand($cities);
            $assignment['destination'] = array_rand(array_filter($cities, function($key) use ($base) {
                return ($key != $base);
            }));
            $assignment['difficulty'] = $difficulties[array_rand($difficulties)];
            
            $expl_str = explode("/", $amount[$assignment['difficulty']]);
            $assignment['amount'] = rand($expl_str[0], $expl_str[1]);
            array_push($assignments, $assignment);
        }
        return $assignments;
    }
    function armyAssignments($count, $difficulty_set = false) {
        
        $rewards = array("easy" => "100/200", "medium" => "230/400", "hard" => "500/800");
        $warriors_required = array("easy" => "3/5", "medium" => "6/10", "hard" => "10/15");
        $difficulties = array("easy", "medium", "hard");
        $time = array("easy" => "1000/2000", "medium" => "3000/4000", "hard" => "5000/6000");
        
        $assignments = array();
        for($i = 0; $i < $count; $i++) {
            $assignment = array();
            $assignment['location'] = array_rand($cities);
            // $difficulty_set is used to determine assigments with a specific difficulty
            if($difficulty_set != false) {
                $difficulty = $difficulty_set;
            }
            else {
                $difficulty = array_rand($difficulties);    
            }  
            // Explode the array string containing min/max value and then using rand() to select a random value between those two
            $warriors_expl = explode("/", $warriors_required[$difficulty]);
            $assignment['warriors_required'] = rand($warriors_expl[0], $warriors_expl[1]);
            $rewards_expl = explode("/", $rewards[$difficulty]);
            $assignment['reward'] = explode("/", $rewards_expl[0], $rewards_expl[1]);
            $time_expl = explode("/", $rewards[$difficulty]);
            $assignment['time'] = rand($time_expl[0], $time_expl[1]);
            array_push($assignments, $assignment);
        }
        return $assignments;
    }
    function makeSQL($type, $data) {
        $sql = array();
        $stmt_start = "INSERT INTO assignments (....) VALUES ";
        switch($type) {
            case 'assignment':
                $stmt_start .= "INSERT INTO assignments (....) ";
                break;
        }
        foreach($data as $key) {
            $stmt = "(";
            // Get the values from key for the for loop so the values are added to $stmt without having to specify index
            $array = array_values($key);
            for($i = 0; $i < count($array); $i++) {
                if($i < count($array) - 1) {
                    $stmt .= $array[$i] . ', '; 
            echo $stmt . "</br>";
                }
          else {
                  $stmt .= $array[$i] . ')';
            echo $stmt . "</br>";
                }
            }
            array_push($sql, $stmt);
        }
        var_dump($sql);
        for($i = 0; $i < count($sql); $i++) {
            $stmt_start += $sql[$i];
        }
        return $stmt_start;
    }
        
    $assignments = assignments(3);
    $statement = makeSQL('assignemnt', $assignments);
    
    print_r($statement);