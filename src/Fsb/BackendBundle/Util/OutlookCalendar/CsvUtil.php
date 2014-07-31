<?php
namespace Fsb\BackendBundle\Util\OutlookCalendar;

use FOS\RestBundle\Decoder\XmlDecoder;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
class CsvUtil
{
	
	static public function csvToArray($filePath)
	{
		$rows = array();
		if (($file = fopen($filePath, "r")) !== FALSE) {
			// Lee los nombres de los campos
			$headers = fgetcsv($file, 0, ",", "\"", "\"");
			$num_fields = count($headers);
			// Lee los registros
			while (($datos = fgetcsv($file, 0, ",", "\"", "\"")) !== FALSE) {
				// Crea un array asociativo con los nombres y valores de los campos
				$row = array();
				for ($ifield = 0; $ifield < $num_fields; $ifield++) {
					$row[$headers[$ifield]] = $datos[$ifield];
				}
				// Añade el registro leido al array de registros
				$rows[] = $row;
			}
			fclose($file);
		}
		
		return $rows;
	}
}