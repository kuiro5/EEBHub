<?php

//START SESSION
session_start();

//INPUTS
//$user = "test";
$user = $_GET['user'];					// get user variable from javascript
$email = "EMPTY";
$buildingName = $_SESSION[building_name] = $_POST[building_name];
$city = $_SESSION[weather_epw_location] = $_POST[weather_epw_location];
$state = "EMPTY";
$primary_spacetype = $_SESSION[activity_type] = $_POST[activity_type];

// define the secondary_spacetypea
switch($primary_spacetype) {
    case "SmallOffice":
    case "MediumOffice":
    case "LargeOffice":
        $_POST[activity_type_specific] = 'WholeBuilding'; 
        break;
    case "Warehouse":
        $_POST[activity_type_specific] = 'Office'; 
        break;
    case "Retail":
        $_POST[activity_type_specific] = 'Core'; 
        break;
}
$secondary_spacetype = $_SESSION[activity_type_specific] = $_POST[activity_type_specific];
    
$floors = $_SESSION[number_of_floors] = $_POST[number_of_floors];
$floorArea = $_SESSION[gross_floor_area] = $_POST[gross_floor_area];
$roofMaterial = $_SESSION[roof_type] = $_POST[roof_type];
$wallMaterial = $_SESSION[exterior_wall_type] = $_POST[exterior_wall_type];
$windowPercent = $_SESSION[window_to_wall_ratio] = $_POST[window_to_wall_ratio] / 100;
$shape = $_SESSION[footprint_shape] = $_POST[footprint_shape];

$server_number = rand(0, 99);

//RUN OPENSTUDIO
$rubyCmdCreateIDF = "xvfb-run -n $server_number ruby run_eebhub.rb ".
            				$user.' '.					
							$email.' '.					
							'"'.$buildingName.'" '.	        			
							$city.' '.					
							$state.' '. 
                            $primary_spacetype.' '.
                            $secondary_spacetype.' '.
                            $floors.' '.
                            $floorArea.' "'.
                            $roofMaterial.'" "'. 
                            $wallMaterial.'" '.
                            $windowPercent.' '.
                            $shape;					
          
// define                  
switch($shape) {
    case "Rectangle": 
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['length'].' '.					
							$_POST['width'];
        break;
        
    case "H": 
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['length'].' '.					
							$_POST['left_width'].' '.
							$_POST['center_width'].' '.					
							$_POST['right_width'].' '.
							$_POST['left_end_length'].' '.					
							$_POST['right_end_length'].' '.
							$_POST['left_upper_end_offset'].' '.					
							$_POST['right_upper_end_offset'];
        break;
        
    case "L":  
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['length'].' '.					
							$_POST['width'].' '.
							$_POST['end_1'].' '.					
							$_POST['end_2'];
        break;
        
    case "T": 
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['length'].' '.					
							$_POST['width'].' '.
							$_POST['end_1'].' '.					
							$_POST['end_2'].' '.
							$_POST['offset'];
        break;
        
    case "U": 
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['length'].' '.					
							$_POST['width_1'].' '.					
							$_POST['width_2'].' '.
							$_POST['end_1'].' '.
							$_POST['end_2'].' '.
							$_POST['offset'];
        break;
        
    case "Pie": 
        $rubyCmdCreateIDF = $rubyCmdCreateIDF.' '.
            				$_POST['radius_a'].' '.					
							$_POST['radius_b'].' '.
							$_POST['num_points'].' '.					
							$_POST['degree'];
        break;
    
}
                           
                           
//echo $rubyCmdCreateIDF;                    
$run = shell_exec($rubyCmdCreateIDF);
echo $run;

// baseline modelname
$_SESSION['cur_model'] = "Simulation_%d.idf", $user;
$_SESSION['eem1_model'] = "EEM1_Simulation_%d.idf", $user;
$_SESSION['eem2_model'] = "EEM2_EEM1_Simulation_%d.idf", $user;
$_SESSION['eem3_model'] = "EEM3_EEM2_EEM1_Simulation_%d.idf", $user;


// create EEM in run 2
//$run2 = shell_exec("xvfb-run -n $server_number ruby run_eem.rb ".$_SESSION['cur_model']);
//echo $run2;


//header("location: http://localhost/tools.eebhub.org");
?>
