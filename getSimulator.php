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
$simulator->id = $_GET["id"];
header('Content-Type: application/json');
echo json_encode($simulator->getSimulator());
?>