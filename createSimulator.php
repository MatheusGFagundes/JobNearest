<?php

use objects\Simulator;
use objects\Resident;
use objects\jobs;
use objects\Job;
//namespace JobNearest;
include 'config/Database.php';
include 'objects/Resident.php';
include 'objects/Job.php';
include 'objects/Simulator.php';
include 'Process.php';
$database = new Database();
$db = $database->getConnection();

$simulator = new Simulator($db);
$jobs = json_decode($_POST['jobs'],true);
$residents = json_decode($_POST['residents'],true);
$simulator->residents_count =  count($residents);
$simulator->jobs_count =  count($jobs);

$id= $simulator->create();
$resident = new Resident($db);
$resident->tb_simulator_id = $id;
$insertResidents = "";
foreach($residents as $resi){ 
    $insert = "(" . $id. "," . $resi["lat"] . "," . $resi["lng"] . "," . $resi["education"] . "," . $resi["salary_range"] . "," . $resi["transport"]  . ")";
    $insertResidents .= $insert . "," ;
    
}
$insertResidents = rtrim($insertResidents,",");
if($resident->create($insertResidents)){

}
$job = new Job($db);
$job->tb_simulator_id = $id;
$insertJobs = "";
foreach($jobs as $j){
    $insert = "(" . $id. "," . $j["lat"] . "," . $j["lng"] . "," . $j["education"] . "," . $j["salary_range"] .")";
    $insertJobs .= $insert . "," ;
}
$insertJobs = rtrim($insertJobs,",");
$job->create($insertJobs);
processingSimulator($db, $id,$simulator->residents_count ,$simulator->jobs_count);
chooseTheBigestScores($db,$id);

?>