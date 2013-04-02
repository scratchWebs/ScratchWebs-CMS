<?php

class swSessionUpdate
{
	public $key;
	
	public $update_type;		// the type of update (ie: delete_gallery)
	public $update_object;		// reference to the object being updated (ie: swImage / swPage / swSection)
	
	public $old_value = array();		// the old value (optional)
	public $new_value = array();		// the new value (optional)
	
	public $is_new = false;			// by default these updates are not creating new objects (used when commiting to the database)
	public $is_delete = false;		// used to detect if we're adding and deleting at the same time (no need to do either in that case!)
	
	public $additional_updates = array();	// It may be that one update effects many objects but we only want to see this as one change
											// this is where we will store the "additional" swSessionUpdate objects associated with this change
	
	public function __construct($update_type,$update_object = NULL)
	{
		$this->update_type = $update_type;
		$this->update_object = $update_object;
	}
	
	public function updateField($fieldName,$newValue)
	{
		if (isset($this->update_object) && property_exists($this->update_object, $fieldName))
		{
			$this->old_value[$fieldName] = $this->update_object->$fieldName;
			$this->new_value[$fieldName] = $newValue;
			$this->update_object->$fieldName = $newValue;
		} else {
			throw new exception('Scratchwebs Error: Error updating field');
		}
	}
	
	public function addAdditionalUpdate(swSessionUpdate $additional_update)	// adds a reference to an additional update
	{
		$additional_update->key = $additional_update->update_object->getUID();
		$this->additional_updates[$additional_update->key] = $additional_update;
	}
	
	// search for the previous update either in the update_object (normal) or in the session (just for page sorts atm)
	public function getPreviousUpdate(swSessionObject $sessionObject)
	{
		if (isset($this->update_object) && array_key_exists( $this->key, $this->update_object->sessionUpdates ))
			return $this->update_object->sessionUpdates[$this->key];
		elseif (array_key_exists( $this->key, $sessionObject->sessionUpdates ))
			return $sessionObject->sessionUpdates[$this->key];
	}
	
	// This swSessinoUpdate gets stored in the sessionObject as well as in the $update_object
	public function save(swSessionObject $sessionObject)
	{
		if (isset($this->update_object)) {
			$this->key = $this->update_type . $this->update_object->getUID();	// create a unique id
		} else {
			$this->key = $this->update_type;									// only happens for page sort
		}
		
		// GET the previous update to see if we can undo or overwrite
		$previousUpdate = $this->getPreviousUpdate($sessionObject);
		
		// check if the previous update is of the same type (ie: deleting an image)
		if (isset($previousUpdate) && $previousUpdate->update_type == $this->update_type) {
			// check if this update is back to the original value
			if ($this->isBackToOriginalValue($previousUpdate)) {
				// undo this and the previous update (as we are back to the original value)
				unset($this->update_object->sessionUpdates[$this->key]);
				unset($sessionObject->sessionUpdates[$this->key]);
				// fire dbObject event to notify the object that the session has updated
				if (isset($this->update_object))
					$this->update_object->event_sessionUpdated($this,$sessionObject);
				return false;
			} else {
				// overwrite the previous update with this new one (maintain the original value)
				$this->old_value = $previousUpdate->old_value;
				
				// maintain the original value for all the additional updates as well
				foreach ($this->additional_updates as $additionalUpdate) {
					$prevAdditionalUpdate = $previousUpdate->additional_updates[$additionalUpdate->key];
					$additionalUpdate->old_value = $prevAdditionalUpdate->old_value;
				}
			}
		}
		
		
		// if we are deleting then we can remove all other updates to that object
		if ($this->is_delete) {
			$this_uid = $this->update_object->getUID();	// make a note of the current update_object uid
			$update_is_new = false;
			
			// remove all previous updates to this object
			foreach ($sessionObject->sessionUpdates as $sessionUpdate)
				if (isset($sessionUpdate->update_object) && $sessionUpdate->update_object->getUID() == $this_uid)
				{
					// make a not if there was an "is_new" update
					if ($sessionObject->sessionUpdates[$sessionUpdate->key]->is_new) $update_is_new = true;
					
					unset($this->update_object->sessionUpdates[$this->key]);
					unset($sessionObject->sessionUpdates[$sessionUpdate->key]);
				}
			
			// fire dbObject event to notify the object that the session has updated (in this case that something has been deleted)
			if (isset($this->update_object))
				$this->update_object->event_sessionUpdated($this,$sessionObject);
			
			// if there was an "is_new" update, then we can exit the function as this delete isn't required
			if ($update_is_new) return false;
		}
		
		// Save the update
		$sessionObject->sessionUpdates[$this->key] = $this;
		
		if (isset($this->update_object)) {
			$this->update_object->sessionUpdates[$this->key] = $this;
			$this->update_object->event_sessionUpdated($this,$sessionObject);		// fire dbObject event to notify the object that the session has updated
		}
		
		return true;
	}

	public function isBackToOriginalValue(swSessionUpdate $previousUpdate)
	{
		$areSame = true;
		
		if ($previousUpdate->old_value == $this->new_value)
		{
			foreach ($this->additional_updates as $additionalUpdate)
				if (array_key_exists($additionalUpdate->key,$previousUpdate->additional_updates))
				{
					$prevAdditionalUpdate = $previousUpdate->additional_updates[$additionalUpdate->key];
					
					if ($prevAdditionalUpdate->old_value != $additionalUpdate->new_value) {
						$areSame = false;
						break;
					}
				} else {
					$areSame = false;
					break;
				}
		} else {
			$areSame = false;
		}
		
		return $areSame;
	}
	
	private function undoField($fieldName)
	{
		if (isset($this->update_object) && property_exists($this->update_object, $fieldName))
		{
			$this->update_object->$fieldName = $this->old_value[$fieldName];
		}
		
		foreach ($this->additional_updates as $additionalUpdate)
		{
			if (isset($additionalUpdate->update_object) && property_exists($additionalUpdate->update_object, $fieldName))
			{
				$additionalUpdate->update_object->$fieldName = $additionalUpdate->old_value[$fieldName];
			}
		}
		
		if (property_exists($this->update_object, $fieldName)) return $this->update_object->$fieldName;
	}
	public function undo(swSessionObject $sessionObject)
	{
		$undoResponse = "";
		
		switch ($this->update_type) {
			// swPortfolio updates
			case "delete_gallery":
				$this->undoField('delete_flag');
				break;
			case "enable_gallery":
				$this->undoField('enabled');
				break;
			case "add_gallery":
				$portfolio = $sessionObject->findFeatureInSession($this->update_object->gallery_fk_portfolio_id,swFeature::FEATURE_TYPE_PORTFOLIO);
				$portfolio->removeGallery($this->update_object);
				break;
			case "sort_galleries":
				$this->undoField('gallery_order');
				$portfolio = $sessionObject->findFeatureInSession($this->update_object->portfolio_id,swFeature::FEATURE_TYPE_PORTFOLIO);
				$portfolio->sortGalleries();
				
				foreach ($portfolio->galleries as $gallery)
				{
					if (!$gallery->isFirstGallery())
						$undoResponse .= ',';
					$undoResponse .= $gallery->gallery_id;
				}
				break;
			
			// swGallery updates
			case "set_main_image":
				$this->undoField('img_featured');
			case "update_image":
				foreach ($this->old_value as $key => $value) {
					$this->undoField($key);
				}
				break;
			case "add_new_image":
				$gallery = $sessionObject->findGalleryInSession($this->update_object->img_fk_gallery_id);
				$gallery->removeImageById($this->update_object->img_id);
				break;
			case "delete_image":
				$this->undoField('delete_flag');
				break;
			case "rename_gallery":
				$this->undoField('gallery_name');
				break;
			case "sort_images":
				$this->undoField('img_order');
				$gallery = $this->update_object;
				$gallery->sortImages();
				
				foreach ($gallery->gallery_images as $image)
				{
					if (!$image->isFirstImage())
						$undoResponse .= ',';
					$undoResponse .= $image->img_id;
				}
				break;
			case "gallery_update_desc_long":
				$undoResponse = $this->undoField('gallery_desc_long');
				break;
			
			// swPage updates
			case "set_title":
				$undoResponse = $this->undoField('pg_title');
				break;
			case "set_linkname":
				$undoResponse = $this->undoField('pg_linkname');
				break;
			case "set_description":
				$undoResponse = $this->undoField('pg_description');
				break;
			case "set_meta_title":
				$undoResponse = $this->undoField('pg_meta_title');
				break;
			case "set_meta_description":
				$undoResponse = $this->undoField('pg_meta_description');
				break;
			case "set_meta_keywords":
				$undoResponse = $this->undoField('pg_meta_keywords');
				break;
			case "page_sort":
				$this->undoField('pg_order');
				$sessionObject->sortPages();
				foreach ($sessionObject->pages as $page)
				{
					if (!$page->isFirstPage())
						$undoResponse .= ',';
					$undoResponse .= $page->pg_id;
				}
				break;
			
			// swSection Updates
			case "section_update_html":
				$this->undoField('section_html');
				$undoResponse = $this->update_object->section_html;
				break;
			
			// swWebLog Updates
			case "weblog_update":
				$this->undoField('wlentry_author');
				$this->undoField('wlentry_text');
				break;
			case "weblog_create":
				$this->update_object->weblog->removeEntry($this->update_object->getObjectID());
				break;
			case "weblog_delete":
				$this->undoField('delete_flag');
				break;
			case "weblog_sort":
				$this->undoField('wlentry_order');
				$weblog = $this->update_object;
				$weblog->sortEntries();
				foreach ($weblog->weblog_entries as $wlentry)
				{
					$undoResponse .= $wlentry->wlentry_id . ',';
				}
				$undoResponse = substr($undoResponse,0,strlen($undoResponse)-1);
				break;
				
			default:
				throw new exception("ScratchWebs Error: Unrecognised update_type in swSessionUpdate->undo()");
				break;
		}
		
		unset($sessionObject->sessionUpdates[$this->key]);
		
		if (isset($this->update_object)) {
			unset($this->update_object->sessionUpdates[$this->key]);
		}
		
		$log = new swLog();
		
		if (isset($sessionUpdate->update_object)) {
			$log->log_object_type = $sessionUpdate->update_object->getObjectType();
			$log->log_object_id = $sessionUpdate->update_object->getObjectID();
		} else {
			// At the moment this only happens for pages
			// TODO: this needs to change because there is no parent object to tie this too
			$log->log_object_type = dbObject::OBJECT_TYPE_PAGE;
			$log->log_object_id = -1;
		}
		
		$log->log_type = swLog::LOG_TYPE_SESSION_UPDATE_UNDO;
		$log->log_message = $this->getDesciption();
		$log->log_fk_user_id = $sessionObject->user->user_id;
		$log->saveAsNew();
		
		foreach ($this->additional_updates as $additional_update)
		{
			$additional_update->undo($sessionObject);
		}  
		
		return $undoResponse;
	}
	
	public function getDesciption()				// a user friendly description of the change (displayed in the commit dialog
	{
		switch ($this->update_type) {
			// swPortfolio updates
			case "delete_gallery":
				return 'Deleted ' . $this->update_object->gallery_type . ' "' . $this->update_object->gallery_name . '"';
				break;
			case "enable_gallery":
				return 'Enable/Disabled ' . $this->update_object->gallery_type . ' "' . $this->update_object->gallery_name . '"';
				break;
			case "add_gallery":
				return 'Added new ' . $this->update_object->gallery_type . ' "' . $this->update_object->gallery_name . '"';
				break;
			case "sort_galleries":
				return $this->update_object->portfolio_name . ' order changed';
				break;
			
			// swGallery updates
			case "set_main_image":
				return 'Image Updated "' . $this->update_object->img_name . '"';
			case "update_image":
				return 'Image updated "' . $this->update_object->img_name . '"';
				break;
			case "add_new_image":
				return 'Image added "' . $this->update_object->img_name . '"';
				break;
			case "delete_image":
				return 'Image deleted "' . $this->update_object->img_name . '"';
				break;
			case "rename_gallery":
				return 'Renamed ' . $this->update_object->gallery_type . ' to "' . $this->update_object->gallery_name . '"';
				break;
			case "sort_images":
				return $this->update_object->gallery_name . ' images sorted';
				break;
			case "gallery_update_desc_long":
				return $this->update_object->gallery_name . ' description updated';
				break;
			
			// swPage updates
			case "set_title":
				return 'Page title changed "' . $this->update_object->pg_title . '"';
				break;
			case "set_linkname":
				return 'Page link name changed "' . $this->update_object->pg_title . '"';
				break;
			case "set_description":
				return 'Page description changed "' . $this->update_object->pg_title . '"';
				break;
			case "set_meta_title":
				return 'Page meta title changed "' . $this->update_object->pg_title . '"';
				break;
			case "set_meta_description":
				return 'Page meta description changed "' . $this->update_object->pg_title . '"';
				break;
			case "set_meta_keywords":
				return 'Page meta keywords changed "' . $this->update_object->pg_title . '"';
				break;
			case "page_sort":
				return 'Page order changed';
				break;
			
			// swSection updates
			case "section_update_html":
				return 'Section updated "' . $this->update_object->section_name . '"';
				$this->undoField('section_html');
				break;
			
			// swWebLog updates
			case "weblog_create":
				return 'New ' . $this->update_object->weblog->weblog_entry_name . ' added (' . $this->update_object->wlentry_author . ')';
				break;
			case "weblog_update":
				return $this->update_object->weblog->weblog_entry_name . ' updated (' . $this->update_object->wlentry_author . ')';
				break;
			case "weblog_delete":
				return $this->update_object->weblog->weblog_entry_name . ' deleted (' . $this->update_object->wlentry_author . ')';
				break;
			case "weblog_sort":
				return $this->update_object->weblog_name . ' sorted';
				break;
			
			default:
				return "Unnamed update";
				break;
		}
	}
	
	public function commitUpdate(&$savedObjects)	// pass in $savedObjects by reference
	{
		if (isset($this->update_object))
		{
			$object_key = $this->update_object->getUID();
			
			if  (!array_key_exists($object_key,$savedObjects))		// don't commit the same object twice
			{
				if ($this->is_new)	$this->update_object->saveAsNew();
				else				$this->update_object->update();
				
				// keep track of what get's saved (as we only need to save each dbObject once)
				$savedObjects[$object_key] = $this->update_object;
				
				// remove the reference to all updates from the dbObject
				$this->update_object->sessionUpdates = array();
			}
		}
		
		foreach ($this->additional_updates as $sessionUpdate) {		// loop through and commit all additional_updates
			$sessionUpdate->commitUpdate($savedObjects);
		}
	}
}

?>