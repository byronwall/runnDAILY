<?php
require("../lib/config.php");

$encoded = (isset($_GET["encoded"]))? $_GET["encoded"]: die();
$distance = (isset($_GET["distance"]))? $_GET["distance"]: die();

$im_width = 100;
$im_height = 100;

$padding = 0.95;

$point_arr = decodePolylineToArray($encoded);

$bg = imagecreatefrompng($site_root."/img/route_bg.png");
//$shadow = imagecreatefrompng($site_root."/img/route_shadow.png");

$im = imagecreatetruecolor($im_width, $im_height) or die('Cannot Initialize new GD image stream');

imageSaveAlpha($bg, true);
ImageAlphaBlending($im, true);
ImageAntiAlias($im, true);
//ImageSetThickness($im, 3);

$black = imagecolorallocatealpha($im, 0x00, 0x00, 0x00, 0);
$clear = imagecolorallocatealpha($im, 200, 200, 200, 127);
$line_color = imagecolorallocatealpha($im, "0x00", "0x3c", "0xff", 0);
imagefill($im, 0, 0, $clear);

$boundingBox = getBoundingBox($point_arr);

$height = $boundingBox["height"];
$width = $boundingBox["width"];

$height = $width = max(array($height, $width));

$center_point = $boundingBox["center"];

$normal_points = array();
$normal_scaled = array();

$i = 0;
while($i < count($point_arr["x"])){
	$x_dist = $point_arr["x"][$i] - $center_point["x"];
	$y_dist = $point_arr["y"][$i] - $center_point["y"];

	$x_norm = $x_dist / $width * $padding;
	$y_norm = $y_dist/$height * $padding;

	$normal_points[] = array("x"=>$x_norm, "y"=>$y_norm);

	$x_scaled = ($x_norm+1)*$im_width/2;
	$y_scaled = ($y_norm+1)*$im_height/2;

	$normal_scaled[] = array("x"=>$x_scaled, "y"=>$y_scaled);

	$i++;
}

for($i = 1; $i < count($normal_points); $i++){
	imageline($im, $normal_scaled[$i-1]["x"], $normal_scaled[$i-1]["y"], $normal_scaled[$i]["x"], $normal_scaled[$i]["y"],$line_color);
}

imagestring($im, 2, 50, 85, $distance." mi", $black);

imagecopy($bg, $im, 5, 5, 0, 0, $im_width, $im_height);
//imagecopy($shadow, $bg, 7, 6, 0, 0, $im_width, $im_height);

header ("Content-type: image/png");
imagepng($bg);
//imagedestroy($shadow);
imagedestroy($im);
imagedestroy($bg);

exit;

function getBoundingBox($points){
	$point_max["x"] = max($points["x"]);
	$point_max["y"] = max($points["y"]);
	$point_min["x"] = min($points["x"]);
	$point_min["y"] = min($points["y"]);

	$width = ($point_max["x"] - $point_min["x"]) / 2;
	$height = ($point_max["y"] - $point_min["y"]) / 2;
	$center = array("x"=>($point_max["x"] + $point_min["x"]) / 2, "y"=>($point_max["y"] + $point_min["y"]) / 2);

	return array("height"=>$height, "width"=>$width, "center"=>$center);
}

function decodePolylineToArray($encoded)
{
	$length = strlen($encoded);
	$index = 0;
	$points = array();
	$lat = 0;
	$lng = 0;

	while ($index < $length)
	{
		// Temporary variable to hold each ASCII byte.
		$b = 0;

		// The encoded polyline consists of a latitude value followed by a
		// longitude value.  They should always come in pairs.  Read the
		// latitude value first.
		$shift = 0;
		$result = 0;
		do
		{
			// The `ord(substr($encoded, $index++))` statement returns the ASCII
			//  code for the character at $index.  Subtract 63 to get the original
			// value. (63 was added to ensure proper ASCII characters are displayed
			// in the encoded polyline string, which is `human` readable)
			$b = ord(substr($encoded, $index++)) - 63;

			// AND the bits of the byte with 0x1f to get the original 5-bit `chunk.
			// Then left shift the bits by the required amount, which increases
			// by 5 bits each time.
			// OR the value into $results, which sums up the individual 5-bit chunks
			// into the original value.  Since the 5-bit chunks were reversed in
			// order during encoding, reading them in this way ensures proper
			// summation.
			$result |= ($b & 0x1f) << $shift;
			$shift += 5;
		}
		// Continue while the read byte is >= 0x20 since the last `chunk`
		// was not OR'd with 0x20 during the conversion process. (Signals the end)
		while ($b >= 0x20);

		// Check if negative, and convert. (All negative values have the last bit
		// set)
		$dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));

		// Compute actual latitude since value is offset from previous value.
		$lat += $dlat;

		// The next values will correspond to the longitude for this point.
		$shift = 0;
		$result = 0;
		do
		{
			$b = ord(substr($encoded, $index++)) - 63;
			$result |= ($b & 0x1f) << $shift;
			$shift += 5;
		}
		while ($b >= 0x20);

		$dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
		$lng += $dlng;

		// The actual latitude and longitude values were multiplied by
		// 1e5 before encoding so that they could be converted to a 32-bit
		// integer representation. (With a decimal accuracy of 5 places)
		// Convert back to original values.
		$points["x"][] = $lng * 1e-5;
		$points["y"][] = -($lat * 1e-5);
	}

	return $points;
}
?>