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
	
	static public function isInTheRange($postcodeOrig, $postcodeDest, $range) {
	
		$request_url = "http://maps.googleapis.com/maps/api/distancematrix/xml?origins=".$postcodeOrig."|UK&destinations=".$postcodeDest."|UK&mode=car&language=en-EN&sensor=false";
	
		$distance = -1;
		
		$xml = simplexml_load_file($request_url) or die("url not loading");
		$status = $xml->status;
		if ($status=="OK") {
			if ($xml->row->element->distance->value)
				$distance = $xml->row->element->distance->value;
		}	
		
		if ($distance >= 0 && $distance < $range*1.6*1000) {
			return true;
		}
			
		return false;
	}
		
}