<?php
namespace Fsb\BackendBundle\Util\OutlookCalendar;

use FOS\RestBundle\Decoder\XmlDecoder;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
class CsvUtil
{
	/**
	 * 
	 * @param String $filePath
	 * @return array
	 */
	static public function csvToArray($filePath)
	{
		$rows = array();
		if (($file = fopen($filePath, "r")) !== FALSE) {
			// Read the name of the fields
			$headers = fgetcsv($file, 0, ",", "\"", "\"");
			$num_fields = count($headers);
			// Read the rows
			while (($datos = fgetcsv($file, 0, ",", "\"", "\"")) !== FALSE) {
				// Create an array with the headres and the values
				$row = array();
				for ($ifield = 0; $ifield < $num_fields; $ifield++) {
					$row[$headers[$ifield]] = $datos[$ifield];
				}
				// Add the row to the rows array
				$rows[] = $row;
			}
			fclose($file);
		}
		
		return $rows;
	}
	
	/**
	 * 
	 * @param array $rows
	 * @return string (csv)
	 */
	static public function arrayToCsv($rows, $filePath)
	{
		if (count($rows) > 0) {
			$content = "";
			$header = "";
			$i = 0;
			foreach ($rows as $row) {
				foreach ($row as $key => $value) {
					if ($i==0) {
						$header = $header.$key.',';
					}
					$content = $content.$value.',';
				}
				$i++;
				$content = substr($content, 0, strlen($content)-1);
				$content = $content."\r";
			}
			$header = substr($header, 0, strlen($header)-1);
			$content = substr($content, 0, strlen($content)-1);
			$content = $header."\r".$content;
			
			
			$handle = fopen($filePath,"w");
			fwrite($handle, $content);
			fclose($handle);
		}
	
		
		return true;
	}
}