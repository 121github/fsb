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
		
}