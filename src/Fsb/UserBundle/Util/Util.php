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
		
}