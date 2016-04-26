<?PHP

include('classes/NexradDecoder.php');

// Base Reflectivity is encoded as a Radial Image, so we'll use
// the Radial Packet Decoder class.
include('classes/RadialPacketDecoder.php');

$reflectivityDecoder = new RadialPacketDecoder();
$reflectivityDecoder->setFileResource('/tmp/sn.last');

// Now we decode all the available blocks.
$headers = $reflectivityDecoder->parseMHB();
$description = $reflectivityDecoder->parsePDB();
$symbology = $reflectivityDecoder->parsePSB();
if($description['graphicoffset'] != 0)
{
	$graphic = $crDecoder->parseGAB();
}


$zoom = 1;
$width = 480 * $zoom;
$height = 480 * $zoom;

$im = @imagecreatetruecolor ($width, $height);
imageantialias($im, true);
imagealphablending($im, true);

$background_color = ImageColorAllocate ($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, $width, $height, $background_color);

$color[0] = ImageColorAllocate ($im, 0, 0, 0);
$color[1] = ImageColorAllocate ($im, 0, 234, 236);
$color[2] = ImageColorAllocate ($im, 1, 160, 246);
$color[3] = ImageColorAllocate ($im, 0, 0, 246);
$color[4] = ImageColorAllocate ($im, 0, 255, 0);
$color[5] = ImageColorAllocate ($im, 0, 200, 0);
$color[6] = ImageColorAllocate ($im, 0, 144, 0);
$color[7] = ImageColorAllocate ($im, 255, 255, 0);
$color[8] = ImageColorAllocate ($im, 231, 192, 0);
$color[9] = ImageColorAllocate ($im, 255, 144, 0);
$color[10] = ImageColorAllocate ($im, 255, 0, 0);
$color[11] = ImageColorAllocate ($im, 214, 0, 0);
$color[12] = ImageColorAllocate ($im, 192, 0, 0);
$color[13] = ImageColorAllocate ($im, 255, 0, 255);
$color[14] = ImageColorAllocate ($im, 153, 85, 201);
$color[15] = ImageColorAllocate ($im, 255, 255, 255);


foreach($symbology['radial'] AS $radialAngle=>$radialData)
{
	$radialPosition = 0;
	
	foreach($radialData['colorValues'] AS $radialPositionColorCode)
	{
		
		$points = array();
		$angleDelta = $radialData['angledelta']; 
		
		$points[] = (cos(deg2rad(($radialAngle - 90) + $angleDelta)) * $radialPosition * $zoom) + ($width / 2);
		$points[] = (sin(deg2rad(($radialAngle - 90) + $angleDelta)) * $radialPosition * $zoom) + ($height / 2);		

		$points[] = (cos(deg2rad($radialAngle - 90)) * $radialPosition  * $zoom) + ($width / 2);
		$points[] = (sin(deg2rad($radialAngle - 90)) * $radialPosition  * $zoom) + ($height / 2);
		
		$points[] = (cos(deg2rad($radialAngle - 90)) * ($radialPosition + 1)  * $zoom) + ($width / 2);
		$points[] = (sin(deg2rad($radialAngle - 90)) * ($radialPosition + 1)  * $zoom) + ($height / 2);
		
		$points[] = (cos(deg2rad(($radialAngle - 90) + $angleDelta)) * ($radialPosition + 1)  * $zoom) + ($width / 2);
		$points[] = (sin(deg2rad(($radialAngle - 90) + $angleDelta)) * ($radialPosition + 1)  * $zoom) + ($height / 2);

		imagefilledpolygon($im, $points, 4, $color[$radialPositionColorCode]);

		$radialPosition++;
	}
}
	
	
header("Content-type: image/png");
imagepng($im);

?>