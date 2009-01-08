<?php
die("The elevation feature is not implemented yet.");

/*
 * This page is used to return elevation data to the mapping features of the site.
 * 
 * This page expects to recieve a start and end point for the current segment along
 * with the number of points on the segment:
 * 
 * start_lat, start_lng, end_lat, end_lng, num_points
 * 
 * This page will return a JSON string containing a list of elevations.
 * 
 */


$start_lat = $_POST["start_lat"];
$start_lng = $_POST["start_lng"];

$end_lat = $_POST["end_lat"];
$end_lng = $_POST["end_lng"];

$num_points = $_POST["num_points"];

//These values need to come from the POST data.
$lat = 46.0;
$lng = 45.0;

//Before we check with USGS, check the cache.

//This will most likely need to be done multiple times.  Look into the multi functions.
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://gisdata.usgs.net/xmlwebservices2/elevation_service.asmx/getElevation");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "X_Value=$lng&Y_Value=$lat&Elevation_Units=&Source_Layer=&Elevation_Only=");

$xml_data = curl_exec($ch);
curl_close($ch);

$xml = simplexml_load_string($xml_data) or die("Invalid XML.");

//Somewhere is this region, the data should be added to the cache.


/*
 * This is the sample return data.  All that really matters is the elevation.
 * 
 * <USGS_Elevation_Web_Service_Query>
<Elevation_Query x="45" y="45">
<Data_Source>SRTM.C_1TO19_3</Data_Source>
<Data_ID>SRTM.C_1TO19_3</Data_ID>
<Elevation>147.637795275591</Elevation>
<Units>FEET</Units>
</Elevation_Query>
</USGS_Elevation_Web_Service_Query>
 * 
 */

//This page will need to return a JSON string.
echo $xml->Elevation_Query->Elevation;

?>