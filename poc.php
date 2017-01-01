<?php 

//default coordinates
$x = 48.7281388; 
$y = 21.2423682;

if (!empty($_GET["lat"])){
	$x = round($_GET["lat"],5);
}

if (!empty($_GET["long"])){
	$y = round($_GET["long"],5);
}

//transfer from standard gps to notation which is being used by ArcGIS
$data = gpstowgs($x,$y);

//load parcel information
$url = "http://mapka.gku.sk/mapka/rest/services/WM/Identify_WM/MapServer/identify?f=json&tolerance=1&returnGeometry=false&imageDisplay=1343%2C744%2C96&geometry={%22x%22%3A".$data["x"]."%2C%22y%22%3A".$data["y"]."}&geometryType=esriGeometryPoint&sr=102100&mapExtent=".$data["x"]."%2C".$data["y"]."%2C".$data["x"]."%2C".$data["y"]."";

$parcel = json_decode(file_get_contents($url));

//get parcel ID
$parcelID = $parcel->results[0]->attributes->ID;

//get owners of said parcel
$owners = file_get_contents('http://mapka.gku.sk/mapovyportal//odata/ParcelsC('.$parcelID.')/Kn.Participants?$filter=Type/Code%20eq%201&$expand=Subjects($expand=Address)'); 

$owners = json_decode($owners);


print_R($owners);

print_r($parcel);

die;

function gpstowgs($yLat,$xLon){

	$semimajorAxis = 6378137.0;  # WGS84 spheriod semimajor axis
    $east = $xLon * 0.017453292519943295;
    $north = $yLat * 0.017453292519943295;
 
    $northing = 3189068.5 * log((1.0 + sin($north)) / (1.0 - sin($north)));
    $easting = $semimajorAxis * $east;
    return (array("y"=>$northing,"x"=>$easting));
}