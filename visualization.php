<?php
use objects\Simulator;
use objects\Resident;
include 'config/Database.php';
include 'objects/Simulator.php';
$database = new Database();
$db = $database->getConnection();

$simulator = new Simulator($db);
$ids = $simulator->getAllSimulatorId();

?>

<!DOCTYPE html>
<html lang="PT-BR">

<head>
    <meta charset="utf-8">

</html>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
<script>
    var Point = function (lat, long, type, id, matchId, distance, duration) {
        this.lat = lat;
        this.long = long;
        this.type = type;
        this.id = id;
        this.matchId = matchId;
        this.distance = distance;
        this.duration = duration;


    }





</script>

<style>
    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */

    #general{
    height: 10%;
    }
    #map {
        height: 90%;
    }

    /* Optional: Makes the sample page fill the window. */
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>


</head>

<body>

    Id das simulações (ultimas são as mais recentes)<select id="simulator_ids">
    <?php foreach($ids as $id): ?>
        <option value="<?php echo  $id["id"] ?>"><?php echo  $id["id"] ?></option>
    <?php endforeach ?>        
    </select>
    
    <button id="save"> Carregar </button>
    <div id="general">
        <input type="checkbox" id="residents" onchange="changeCheckbox()" checked />Residents
        <input type="checkbox" id="jobs" onchange="changeCheckbox()" checked />Jobs
    </div>

    <div id="results">

    </div>
    
    <div id="map"></div>
    <script>

        // This example requires the Geometry library. Include the libraries=geometry
        // parameter when you first load the API. For example:
        // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=geometry">
        var markers = [];
        var directionsDisplay;
        var directionsService;
        function deleteMarkers(){
            for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);

                }
                markers = [];    
        }
        $("#save").click(function () {
            deleteMarkers();
            
            $.ajax({
                type: 'Get',
                data: { "id": document.getElementById("simulator_ids").options[document.getElementById("simulator_ids").selectedIndex].value },
                url: 'getSimulator.php',
                datatype: 'json',
                success: function (data) {
                    
                    var date = new Date(null);
                    date.setSeconds(parseFloat(data["statistcs"]["mean_duration"]).toFixed(0)); // specify value for SECONDS here
                    var result = date.toISOString().substr(11, 8);

                    var date2 = new Date(null);
                    date2.setSeconds(parseFloat(data["statistcs"]["std_duration"]).toFixed(0)); // specify value for SECONDS here
                    var result2 = date2.toISOString().substr(11, 8);
                    document.getElementById('results').innerHTML =
                        "Resultados das Simulações  "  + 
                        " <hr> Total Residencias: " +  data["itens"].length +
                        " <hr> Total de trabalhos " +  data["itens"].length +
                        " <hr> Média do Score: " + parseFloat(data["statistcs"]["mean_score"]).toFixed(2) +
                        " <hr> Desvio Padrão do Score: " + parseFloat(data["statistcs"]["std_score"]).toFixed(2) +
                        " <hr> Média Distância: " + parseFloat(data["statistcs"]["mean_distance"]).toFixed(2) +"(" + parseFloat((data["statistcs"]["mean_distance"]/1000).toFixed(2)) + "Km)" +
                        " <hr> Desvio Padrão da Distância: " + parseFloat(data["statistcs"]["std_distance"]).toFixed(2) +"(" + parseFloat((data["statistcs"]["std_distance"]/1000).toFixed(2)) + "Km)" +
                        " <hr> Média da Duração: " + parseFloat(data["statistcs"]["mean_duration"]).toFixed(2) +"(" + result + ")" +
                        " <hr> Desvio Padrão Duração: " + parseFloat(data["statistcs"]["std_duration"]).toFixed(2)+"(" + result2 + ")";
                    data = data["itens"];
                    for (i = 0; i < data.length; i++) {
                        setMarker(new Point(parseFloat(data[i]["j_lat"]), parseFloat(data[i]["j_long"]), 'job', data[i]["j_id"], data[i]["r_id"], data[i]["distance"], data[i]["route_time"]));
                        setMarker(new Point(parseFloat(data[i]["r_lat"]), parseFloat(data[i]["r_long"]), 'resident', data[i]["r_id"], data[i]["j_id"], data[i]["distance"], data[i]["route_time"]));
                    }
                }
            });
        });

        function changeCheckbox() {
            if (!document.getElementById('jobs').checked) {
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].type == 'job') {
                        markers[i].setMap(null);
                    }

                }
            }
            else {
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].type == 'job') {
                        markers[i].setMap(map);
                    }

                }
            }
            if (!document.getElementById('residents').checked) {
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].type == 'resident') {
                        markers[i].setMap(null);
                    }

                }
            }
            else {
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].type == 'resident') {
                        markers[i].setMap(map);
                    }
                }

            }
        }
        function setMarker(object) {
            var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
            var position = { lat: object.lat, lng: object.long };
            var icon;
            if (object.type == 'resident') {
                icon = {
                    url: iconBase + 'parking_lot_maps.png',
                    scaledSize: new google.maps.Size(20, 20)
                }
            }
            else {
                icon = {
                    url: iconBase + 'info-i_maps.png',
                    scaledSize: new google.maps.Size(20, 20)
                }

            }
            var date = new Date(null);
            date.setSeconds(object.duration); // specify value for SECONDS here
            var result = date.toISOString().substr(11, 8);
            var infoWindow = new google.maps.InfoWindow({
                content: (parseFloat(object.distance) / 1000).toFixed(2) + 'km <br> Tempo:' + result
            });
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: icon,
                title: 'weight: ' + object.matchId,
                type: object.type,
                id: object.id,
                match_id: object.matchId,
                point: object,
                info: infoWindow
            });

            markers.push(marker);

            google.maps.event.addListener(marker, 'click', function () {
                clearMap(this.id, this.match_id);
            });
            google.maps.event.addListener(infoWindow, 'closeclick', function () {
                returnMarkers(); // then, remove the infowindows name from the array
            });

        }
        function calculateAndDisplayRoute(resident, job) {
            directionsService.route({
                origin: resident.position,  // Haight.
                destination: job.position,  // Ocean Beach.
                // Note that Javascript allows us to access the constant
                // using square brackets and a string value as its
                // "property."
                travelMode: google.maps.TravelMode["TRANSIT"]
            }, function (response, status) {
                if (status == 'OK') {
                    directionsDisplay.setDirections(response);
                    directionsDisplay.setMap(map);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }

        function clearMap(id, matchId) {
            var resident, job;
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id != id && markers[i].id != matchId) {
                    markers[i].setMap(null);
                }
                else {
                    markers[i].info.open(map, markers[i]);
                    if (markers[i].point.type == "resident") {
                        resident = markers[i];
                    }
                    else {
                        job = markers[i];
                    }


                }
            }
            calculateAndDisplayRoute(resident, job);

        }
        function returnMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
                markers[i].info.close();

            }
            directionsDisplay.setMap(null);
            map.setZoom(11);
            map.setCenter({ lat: -23.6020214, lng: -46.6721032 });
        }

        function initMap() {

            directionsDisplay = new google.maps.DirectionsRenderer;
            directionsService = new google.maps.DirectionsService;
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: -23.6020214, lng: -46.6721032 },
                zoom: 11,
            });



            var triangleCoords = [
                { lat: -23.345455, lng: -46.813124 },
                { lat: -23.350642, lng: -46.541594 },
                { lat: -23.458428, lng: -46.530734 },
                { lat: -23.462488, lng: -46.364213 },
                { lat: -23.646552, lng: -46.386521 },
                { lat: -23.664358, lng: -46.609384 },
                { lat: -23.891345, lng: -46.624447 },
                { lat: -23.900995, lng: -46.805319 }
            ];

            cidadeSaoPaulo = new google.maps.Polygon({ paths: triangleCoords });



        }



    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTD_YLoIDT6mRJB4exOpNiCyDQyc0cQQI&libraries=geometry&callback=initMap"
        async defer></script>