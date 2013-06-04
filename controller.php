<?php defined('C5_EXECUTE') or die(_('Access Denied.'));

class AttributeListPackage extends Package {

	protected $pkgHandle = 'attribute_list';
	protected $appVersionRequired = '5.6';
	protected $pkgVersion = '0.1.4.3';
	
	public function getPackageDescription() {
		return t("Adds an Attribute Listing attribute. (yup)");
	}
	
	public function getPackageName() {
		return t("Attribute List");
	}
	
	public function install() {
		$pkg = parent::install();
		$this->configure();
	}

	public function upgrade() {
		$this->configure();
		parent::upgrade();
	}

	public function configure() {
		$this->verifyAttribute();
	}

	public function uninstall() {
		parent::uninstall();
		$db = Loader::db();
		$db->Execute('DROP TABLE IF EXISTS atAttributeListSettings');
		$db->Execute('DROP TABLE IF EXISTS atAttributeList');
	}

	public function verifyAttribute() {

		$pkg = Package::getByHandle($this->pkgHandle);
		$attribute_type_handle = 'attribute_list';
		$attribute_type_name = Loader::helper('text')->unhandle($attribute_type_handle);

		// get a few attribute type categories for the attributes to attach
		//Loader::model('attribute/categories/collection');
		$collection_category = AttributeKeyCategory::getByHandle('collection');

		// get attribute to check for existence
		$at = AttributeType::getByHandle($attribute_type_handle);

		// nope? ok lets go
		if(!is_object($at) || !intval($at->getAttributeTypeID()) ) { 

			// add attribute to system
			$at = AttributeType::add($attribute_type_handle, t($attribute_type_name), $pkg);

			// associate with collections
			$collection_category->associateAttributeKeyType($at);

		} else {

			// this actually refreshes the attribute's db tables
			$path = $at->getAttributeTypeFilePath(FILENAME_ATTRIBUTE_DB);
			Package::installDB($path);

			// update the search index in the case that we have modified the db tables
			foreach (CollectionAttributeKey::getList() as $ak) {
				if ($ak->getAttributeType()->getAttributeTypeHandle() == $attribute_type_handle) {
					$ak->updateSearchIndex();
				}
			}

		}

	}


}
