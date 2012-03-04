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
	const OBJECT_TYPE_PAGESTAT = 8;
	
	public $delete_flag = false;
	public $enabled = true;
	
	public $sessionUpdates = array();	// an array of swSessionUpdates that are relevant to this object
	
	abstract public function getObjectType();
	abstract public function getUID();
	abstract public function createFromId($id);
	abstract public function saveAsNew();
	abstract public function update();
	abstract public function getTableName();
	abstract public function createTable();
	
	public function hasUpdates()
	{
		return (count($this->sessionUpdates) > 0) ? true : false;
	}
	
	public function getUpdateKeyByType($update_tyoe)
	{
		foreach ($this->sessionUpdates as $sessionUpdate)
			if ($sessionUpdate->update_type == $update_tyoe) {
				return $sessionUpdate->key;
			}
	}
	
	public function event_sessionUpdated(swSessionUpdate $sessionUpdate,swSessionObject $sessionObject)
	{
		// override this where applicable
	}
}

?>