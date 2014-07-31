<?php

namespace Fsb\CalendarBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Import
 *
 */
class Import
{
    /**
     *
     */
    private $recruiter;
    
    /**
     *
     */
    private $project;

	/**
	 * @Assert\File(
	 * 		maxSize = "500k",
	 * 		mimeTypes = {"text/calendar", "text/csv", "text/plain"},
     *      mimeTypesMessage = "Please upload a valid file (*.ics, *.csv)"
	 * )
	 */
    private $file;
	
    /**
     * 
     * @var unknown
     */
    private $filePath;
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->recruiter;
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
	 * @return \Fsb\CalendarBundle\Entity\Import
	 */
	public function setRecruiter($recruiter) {
		$this->recruiter = $recruiter;
		return $this;
	}
	
	/**
	 * @param UploadedFile $file
	 */
	public function setFile(UploadedFile $file)
	{
		$this->file = $file;
	}
	/**
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}
	
	public function uploadFile($uploadDir)
	{
		if (null === $this->file) {
			return;
		}
		
		$extension = $this->file->getClientOriginalExtension();
		$currentDate = new \DateTime('now');
		$fileName = $currentDate->format('YmdHis').'_'.uniqid().'.'.$extension;
		$this->file->move($uploadDir, $fileName);
		$this->setFilePath($fileName);
	}
	
	/**
	 * 
	 * @return \Fsb\CalendarBundle\Entity\unknown
	 */
	public function getFilePath() {
		return $this->filePath;
	}
	
	
	/**
	 * 
	 * @param unknown $filePath
	 * @return \Fsb\CalendarBundle\Entity\Import
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
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
	 * @return \Fsb\CalendarBundle\Entity\Import
	 */
	public function setProject($project) {
		$this->project = $project;
		return $this;
	}
	
	
	
		

}
