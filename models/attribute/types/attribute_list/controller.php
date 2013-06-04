<?php defined('C5_EXECUTE') or die(_('Access Denied.'));

class AttributeListAttributeTypeController extends AttributeTypeController { 

	//protected $searchIndexFieldDefinition = 'X NULL';
	protected $atTableValues = 'atAttributeList';
	protected $atTableSettings = 'atAttributeListSettings';
	protected $helpers = array('text', 'form');

	/**
	 * Core function to retrieve an array of AttributeKey IDs assigned to this AttributeKey
	 */
	public function getValue() {
		$this->load();
		$db = Loader::db();
		$keys = $db->getAll('SELECT * FROM ' . $this->atTableValues . ' WHERE avID = ? ORDER BY display_order', array($this->getAttributeValueID()));
		foreach ($keys as $i => $key) {
			$keys[$i]['ak'] = $this->attribute_category->getAttributeKeyByID($key['attribute_key_id']);
		}
		return $keys;
	}

	/**
	 * Configure the instance of the AttributeKey to utilize a specific AttributeCategory
	 */
	public function type_form() {
		$this->load();
		$this->set('attribute_categories_list', $this->getAttributeKeyCategoriesList());
	}

	/**
	 * Choose the actual AttributeKeys to add to the list
	 */
	public function form(){
		$this->load();
		$this->set('attribute_keys_list', $this->getAttributeKeysList());
		$this->set('attribute_keys', $this->getValue());
	}

	public function searchForm($list) {
		// $db = Loader::db();
		// $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		// return $list;
	}	
	
	public function search() { 
		//print $form->text($this->field('value'), $value);
	}	 
	
	/**
	 * Sets up all of our data
	 */
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) { return false; }
		if (is_object($this->attribute_category)) { return true; }
		
		$db = Loader::db();
		$attribute_category_id = $db->getOne('SELECT attribute_category_id FROM ' . $this->atTableSettings . ' WHERE akID = ?', $ak->getAttributeKeyID());
		$this->attribute_category = AttributeKeyCategory::getByID($attribute_category_id);
		$this->set('attribute_category_id', $attribute_category_id);
	}

	/**
	 * CRUD Methods
	 */
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('DELETE FROM ' . $this->atTableSettings . ' WHERE akID = ?', array($id));
		}
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('DELETE FROM ' . $this->atTableValues . ' WHERE avID = ?', array($this->getAttributeValueID()));
	}

	public function saveKey($data) {
		$attribute_category_id = (intval($data['attribute_category_id']) > 0)  ? intval($data['attribute_category_id']) : 1; // defaults to collection
		$this->setAttributeCategory($attribute_category_id);
	}
	
	public function setAttributeCategory($attribute_category_id) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$ret = $db->Replace($this->atTableSettings, array(
			'akID' => $ak->getAttributeKeyID(), 
			'attribute_category_id' => intval($attribute_category_id)
		), array('akID'), true);
	}
	
	public function validateForm($p) {
		return $p['value'] != false;
	}

	public function saveForm() {
		$this->saveAllValues($_POST);
	}

	public function saveAllValues($data){
		if (is_array($data['attribute_key_ids'])) {
			foreach ($data['attribute_key_ids'] as $display_order => $key_id) {
				$this->saveValue($display_order, $key_id);
			}
		}
	}

	public function saveValue($display_order, $key_id) {
		$db = Loader::db();
		$db->query('INSERT INTO ' . $this->atTableValues . ' (avID, attribute_key_id, display_order) VALUES (?, ?, ?)', array(
			$this->getAttributeValueID(),
			intval($key_id),
			intval($display_order)
		));		

	}


	/******************************************
	 * Value Methods
	 *
	 * 

	/**
	 * Returns a nicely formatted value for displaying the attribute statically
	 */
	public function getDisplayValue() {
		return $this->getValue();
	}

	/** TODO UDPATE THIS
	 * Returns an array of the currently selected AttributeKeys
	 */
	public function getListValue() {
		$this->load();
		$attribute_key_ids = $this->getValue();
		$attribute_keys = array();
		foreach ($attribute_key_ids as $key_id) {
			$attribute_keys[] = $this->attribute_category->getAttributeKeyByID($key_id);
		}
		return $attribute_keys;
	}


	/******************************************
	 * Utility Functions
	 * 
	 *

	/**
	 * Returns an array of attribute categories
	 */
	public function getAttributeKeyCategories() {
		return AttributeKeyCategory::getList();
	}

	/**
	 * Returns a FormHelper-friendly array of attribute categories in the form of $key => $name
	 */
	public function getAttributeKeyCategoriesList() {
		$text = Loader::helper('text');
		$attribute_key_categories = $this->getAttributeKeyCategories();
		$categories = array();
		foreach ($attribute_key_categories as $category) {
			$categories[$category->getAttributeKeyCategoryID()] = $text->unhandle($category->getAttributeKeyCategoryHandle());
		}
		return $categories;
	}

	/**
	 * Returns an array of attribute keys for this category
	 */
	public function getAttributeKeys() {
		//Loader::model('attribute/key');
		$this->load();
		return AttributeKey::getList($this->attribute_category->getAttributeKeyCategoryHandle());
	}
	
	/**
	 * Returns a FormHelper-friendly array of attribute keys in the form of $key => $name
	 */
	public function getAttributeKeysList() {
		$attribute_keys = $this->getAttributeKeys();
		$keys = array();
		foreach ($attribute_keys as $key) {
			$keys[$key->getAttributeKeyID()] = $key;
		}
		return $keys;
	}


}