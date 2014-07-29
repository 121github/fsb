<?php

namespace Fsb\AppointmentBundle\Entity;

/**
 * Appointment Rest
 *
 */
class AppointmentRest
{
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
	private $recruiter;
	
	/**
	 * 
	 */
	private $appointmentSetter;
	
	/**
	 * 
	 */
	private $title;
	
	/**
	 * 
	 */
	private $comment;
	
	/**
	 * 
	 */
	private $project;
    
	/**
	 * 
	 */
	private $add1;
	
	/**
	 *
	 */
	private $add2;
	
	/**
	 *
	 */
	private $add3;
	
	/**
	 *
	 */
	private $postcode;
	
	/**
	 *
	 */
	private $town;
	
	/**
	 *
	 */
	private $country;
	
	/**
	 *
	 */
	private $recordRef;
    
	
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->title;
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
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
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
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setEndDate($endDate) {
		$this->endDate = $endDate;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getRecruiter() {
		return $this->recruiter;
	}
	
	/**
	 * 
	 * @param unknown $recruiter
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setRecruiter($recruiter) {
		$this->recruiter = $recruiter;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getAppointmentSetter() {
		return $this->appointmentSetter;
	}
	
	/**
	 * 
	 * @param unknown $appointmentSetter
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setAppointmentSetter($appointmentSetter) {
		$this->appointmentSetter = $appointmentSetter;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * 
	 * @param unknown $title
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getComment() {
		return $this->comment;
	}
	
	/**
	 * 
	 * @param unknown $comment
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setComment($comment) {
		$this->comment = $comment;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getProject() {
		return $this->project;
	}
	
	/**
	 * 
	 * @param unknown $project
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setProject($project) {
		$this->project = $project;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getAdd1() {
		return $this->add1;
	}
	
	/**
	 * 
	 * @param unknown $add1
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setAdd1($add1) {
		$this->add1 = $add1;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getAdd2() {
		return $this->add2;
	}
	
	/**
	 * 
	 * @param unknown $add2
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setAdd2($add2) {
		$this->add2 = $add2;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getAdd3() {
		return $this->add3;
	}
	
	/**
	 * 
	 * @param unknown $add3
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setAdd3($add3) {
		$this->add3 = $add3;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getPostcode() {
		return $this->postcode;
	}
	
	/**
	 * 
	 * @param unknown $postcode
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getTown() {
		return $this->town;
	}
	
	/**
	 * 
	 * @param unknown $town
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setTown($town) {
		$this->town = $town;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getCountry() {
		return $this->country;
	}
	
	/**
	 * 
	 * @param unknown $country
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setCountry($country) {
		$this->country = $country;
		return $this;
	}
	
	/**
	 * 
	 */
	public function getRecordRef() {
		return $this->recordRef;
	}
	
	/**
	 * 
	 * @param unknown $recordRef
	 * @return \Fsb\AppointmentBundle\Entity\AppointmentRest
	 */
	public function setRecordRef($recordRef) {
		$this->recordRef = $recordRef;
		return $this;
	}
}
