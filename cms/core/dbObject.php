<?php

abstract class dbObject
{
	const OBJECT_TYPE_PAGE = 0;
	const OBJECT_TYPE_SECTION = 2;
	const OBJECT_TYPE_IMAGE = 3;
	const OBJECT_TYPE_GALLERY = 4;
	const OBJECT_TYPE_PORTFOLIO = 5;
	const OBJECT_TYPE_USER = 6;
	const OBJECT_TYPE_LOG = 7;
	const OBJECT_TYPE_STAT = 8;
	const OBJECT_TYPE_WEBLOG = 9;
	const OBJECT_TYPE_WEBLOGENTRY = 10;
	
	public $delete_flag = false;
	public $enabled = true;
	
	public $sessionUpdates = array();	// an array of swSessionUpdates that are relevant to this object
	
	abstract public function getObjectID();
	abstract public function getObjectType();
	abstract public function getUID();
	abstract public function createFromId($id);
	abstract public function saveAsNew();
	abstract public function update();
	abstract public function getTableName();
	abstract public function createTable();
	
	public function isNew()
	{
		$isNew = false;
		
		foreach ($this->sessionUpdates as $sessionUpdate)
		{
			if ($sessionUpdate->is_new) {
				$isNew = true;
				break;
			}
		}
		
		return $isNew;
	}
	///////these functions should be combined///////////////////////////////////////////////////////////////////
	public function hasUpdates()
	{
		return (count($this->sessionUpdates) > 0) ? true : false;
	}
	///////these functions should be combined///////////////////////////////////////////////////////////////////	
	abstract public function noUpdates();	
	
	public function getUpdateKeyByType($update_type)
	{
		foreach ($this->sessionUpdates as $sessionUpdate)
		{
			if ($sessionUpdate->update_type == $update_type) {
				return $sessionUpdate->key;
			}
		}
	}
	
	public function event_sessionUpdated(swSessionUpdate $sessionUpdate,swSessionObject $sessionObject)
	{
		// override this where applicable
	}
}

?>