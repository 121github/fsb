<?php
namespace Fsb\UserBundle\Util;

class Util
{
	
	static public function setCreateAuditFields($entity, $user_id)
	{
		$entity->setCreatedBy($user_id);
		$entity->setCreatedDate(new \DateTime('now'));
		$entity->setModifiedBy($user_id);
		$entity->setModifiedDate(new \DateTime('now'));
		
		return $entity;
	}
	
	static public function setModifyAuditFields($entity, $user_id)
	{
		$entity->setModifiedBy($user_id);
		$entity->setModifiedDate(new \DateTime('now'));
	
		return $entity;
	}
	
	
	static public function getMapUrl($lat, $lon, $postcode) {
		
		$url = 'https://www.google.co.uk/maps/place/'.$postcode.'/@'.$lat.','.$lon;
		
		return $url; 
	}

	
	static public function postcodeToCoords($postcode){
		//Contact the google maps api to get the lat & `long` from the postcode
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postcode) . ',UK&sensor=false';
		$json = json_decode(file_get_contents($url));
		
		if (!empty($json->results)) {
			$coord = array(
					'lat' => $json->results[0]->geometry->location->lat,
					'lng' => $json->results[0]->geometry->location->lng
			);
		}
		else {
			$coord = array(
				'lat' => null,
				'lng' => null
			);
		}		
		return $coord;
	}
	
	
	static public function getDistance($latOrig, $lonOrig, $latDest, $lonDest) {
	
		$distance = ((acos(sin($latOrig*pi()/180)*sin($latDest*pi()/180) + cos($latOrig*pi()/180)*cos($latDest*pi()/180) * cos(($lonOrig - $lonDest)*pi()/180)))*180/pi())*160*1.1515;
		
		
		$distance = round($distance, 2);
		 
		return $distance;
	}
	
	/**
	 * With an id we need to get a color by the mod
	 * @param unknown $id
	 */
	static public function getColorById($id) {
		$trueColors = array('#000000','#0000FF','#8A2BE2','#A52A2A','#5F9EA0','#D2691E','#FF7F50','#6495ED','#DC143C','#00008B','#008B8B','#B8860B','#006400','#556B2F','#FF8C00','#9932CC','#8B0000','#483D8B','#2F4F4F','#00CED1','#9400D4','#FF1493','#696969','#1E90FF','#B22222','#228B22');
		
		return $trueColors[$id % count($trueColors)];
	}
		
}