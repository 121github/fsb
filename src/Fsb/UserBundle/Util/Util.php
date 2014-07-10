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
	
	
	static public function setLatLonAddress($address, $postcode) {
		$request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($postcode).",uk&sensor=true";
		$xml = simplexml_load_file($request_url) or die("url not loading");
		$status = $xml->status;
		if ($status=="OK") {
			$lat = $xml->result->geometry->location->lat;
			$lon = $xml->result->geometry->location->lng;
			
			$address->setLat($lat);
			$address->setLon($lon);
		}

		return $address;
	}
	
	static public function getMapUrl($lat, $lon, $postcode) {
		
		$url = 'https://www.google.co.uk/maps/place/'.$postcode.'/@'.$lat.','.$lon;
		
		return $url; 
	}

	
	static public function postcodeToCoords($postcode){
		//Contact the google maps api to get the lat & `long` from the postcode
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postcode) . '&sensor=false';
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
		
}