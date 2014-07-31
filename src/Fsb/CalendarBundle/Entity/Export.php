<?php

namespace Fsb\CalendarBundle\Entity;


/**
 * Export
 *
 */
class Export
{

	
	/**
	 * 
	 */
	private $dateRangeType;
	
	/**
	 * 
	 */
	private $startDate;
	
	/**
	 *
	 */
	private $endDate;
	
	/**
	 * 
	 */
	private $exportType;
	
	/**
	 * 
	 */
	private $filter;
	
	/**
	 * 
	 */
	public function getDateRangeType() {
		return $this->dateRangeType;
	}
	
	/**
	 * 
	 * @param unknown $dateRangeType
	 * @return \Fsb\CalendarBundle\Entity\Export
	 */
	public function setDateRangeType($dateRangeType) {
		$this->dateRangeType = $dateRangeType;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getStartDate() {
		return $this->startDate;
	}
	
	/**
	 * 
	 * @param unknown $startDate
	 * @return \Fsb\CalendarBundle\Entity\Export
	 */
	public function setStartDate($startDate) {
		$this->startDate = $startDate;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getEndDate() {
		return $this->endDate;
	}
	
	/**
	 * 
	 * @param unknown $endDate
	 * @return \Fsb\CalendarBundle\Entity\Export
	 */
	public function setEndDate($endDate) {
		$this->endDate = $endDate;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getExportType() {
		return $this->exportType;
	}
	
	/**
	 * 
	 * @param unknown $exportType
	 * @return \Fsb\CalendarBundle\Entity\Export
	 */
	public function setExportType($exportType) {
		$this->exportType = $exportType;
		return $this;
	}
	
	
	/**
	 * Set filter
	 *
	 * @param \Fsb\AppointmentBundle\Entity\AppointmentFilter $filter
	 * @return Appointment
	 */
	public function setFilter(\Fsb\AppointmentBundle\Entity\AppointmentFilter $filter = null)
	{
		$this->filter = $filter;
	
		return $this;
	}
	
	/**
	 * Get filter
	 *
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentFilter
	 */
	public function getFilter()
	{
		return $this->filter;
	}
}
