<?php
namespace Fsb\UserBundle\Util;

class Util
{
	
	static public function setCreateAuditFields($entity)
	{
		$entity->setCreatedBy(1);
		$entity->setCreatedDate(new \DateTime('now'));
		$entity->setModifiedBy(1);
		$entity->setModifiedDate(new \DateTime('now'));
		
		return $entity;
	}
	
	static public function setModifyAuditFields($entity)
	{
		$entity->setModifiedBy(1);
		$entity->setModifiedDate(new \DateTime('now'));
	
		return $entity;
	}
	
}