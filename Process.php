<?php 
use objects\Simulator;
use objects\Resident;
use objects\Job;



function processingSimulator($conn, $id, $jobs, $residents)
{
	set_time_limit(0);
	$max = ($jobs * $residents);
	$index = 0;
	while ($max > $index) {
		$query = "SELECT  ( 6353 * acos( cos( radians(job.lat) ) * cos( radians( r.lat ) ) 
		* cos( radians(r.long) - radians(job.long)) + sin(radians(job.lat)) 
		* sin( radians(r.lat)))) AS distance , job.id as j_id, job.education as j_education, job.salary_range as j_salary_range,r.id as r_id,  r.education as r_education, r.salary_range as r_salary_range FROM `tb_jobs` as job join tb_residents as r WHERE r.tb_simulator_id = " . $id . " and job.tb_simulator_id = " . $id . " limit " . $index . ",100000";

	// prepare query statement
		$stmt = $conn->query($query);
		if ($stmt->num_rows > 0) {
			$inserts = "";
			while ($row = $stmt->fetch_assoc()) {
				$dist = $row["distance"];
				$resident["sal"] = $row["r_salary_range"];
				$resident["escola"] = $row["r_education"];
				$job["sal"] = $row["j_salary_range"];
				$job["escola"] = $row["j_education"];
				$score = scoreRelac($resident, $job, $dist);
				$insert = "(" . $row["r_id"] . "," . $row["j_id"] . "," . $score . "," . $id . ")";
				$inserts = $inserts . $insert . ",";
			}
			$inserts = rtrim($inserts, ",");
			$query = "INSERT INTO `tb_residents_has_tb_jobs`(`tb_residents_id`, `tb_jobs_id`, `score`, `simulator_id`) VALUES " . $inserts;
			$stmt2 = $conn->prepare($query);
			if ($stmt2->execute()) {

			}

		} else {
			echo "0 results";
		}
		$index += 100000;
	}
}

function chooseTheBigestScores($conn, $id)
{
	$x = 0;
	$a = $conn;
	while (true) {
		$query = "SELECT * FROM `tb_residents_has_tb_jobs`where `match` is null and simulator_id = " . $id . " order by score DESC LIMIT 1";

		$stmt = $conn->query($query);
		$x++;
		if ($stmt->num_rows > 0) {
			$row = $stmt->fetch_assoc();
			$result = getInformationByGoogle($a, $row["tb_residents_id"], $row["tb_jobs_id"]);
			$sql = "UPDATE `tb_residents_has_tb_jobs` SET `match` = 1  ,`distance` = " . $result['distance'] . ",`route_time`= " . $result['duration'] . " WHERE tb_residents_id = " . $row["tb_residents_id"] . " and tb_jobs_id = " . $row["tb_jobs_id"];
			$conn->query($sql);
			$sql = "DELETE FROM `tb_residents_has_tb_jobs` WHERE tb_residents_id = " . $row["tb_residents_id"] . " xor tb_jobs_id = " . $row["tb_jobs_id"];
			$conn->query($sql);
		} else {
			break;
		}
	}
}
		//Instanciando objetos
function getInformationByGoogle($coon, $residentId, $jobId)
{

	$resident = new Resident($coon);
	$resident->id = $residentId;
	$resident->readOne();
	$job = new Job($coon);
	$job->id = $jobId;
	$job->readOne();

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://maps.googleapis.com/maps/api/directions/json?origin=" . $resident->lat . "," . $resident->long . "&destination=" . $job->lat . "," . $job->long . "&mode=TRANSIT&key=AIzaSyBX_6UMT0rWwEAHBVJnfPmKjbsag6Bnc3M",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);
	$route = null;
	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		$route = json_decode($response, true);

	}


	$result['distance'] = $route['routes']['0']['legs']['0']['distance']['value'];
	$result['duration'] = $route['routes']['0']['legs']['0']['duration']['value'];
	return $result;
}
function scoreRelac($cand, $vaga, $dist)
{
			/*Score do Salário
			-------------------------------------------------
			Faixa Salarial:
			De 1 a 3 S.M. = 1
			De 3 a 4 S.M. = 2
			De 4 a 6 S.M. = 3
			De 6 a 10 S.M. = 4
			Acima de 10 S.M. = 5
	 */
	$difSal = $cand['sal'] - $vaga['sal'];
	if ($difSal >= 0) {
		$scrSal = (6 * (-$difSal ^ 2)) + 100;
	} else {
		$difSal = abs($difSal);
		$scrSal = round((3.1 * (-$difSal ^ 2)) + 100);
	}
	
			/*Score da Escolaridade
			-------------------------------------------------
			Escolaridade:
			Analfabeto = 1
			Fundamental Incompleto = 2
			Fundamental Completo = 3 
			Médio Incompleto = 4
			Médio Completo = 5
			Superior Incompleto = 6
			Superior Completo = 7
	 */
	$difEsc = abs($cand['escola'] - $vaga['escola']);
	$scrEsc = round((18 * pow($difEsc, 2 / 3) + 41));

			//Score da Experiência

	$scrComp = rand(500, 1000) / 1000;


	if ($dist > 69) {
		$scrDist = 2;
	} else {
		$scrDist = (cos(($dist / 22)) * 48) + 50;
	}
	
	//Score Geral (salario + escolaridade + distância com peso 3)/5 multiplicado pela compatibilidade
	$scrGeral = round((($scrSal + $scrEsc + ($scrDist * 3)) / 5) * $scrComp);
	return $scrGeral;

}

?>
