<?php
class Section extends Page {
	static $icon  = 'section/images/section';
 	static $num_pages_options = array(
		0 => 'All',
		2 => '2',
		3 => '3',
		4 => '4',
		5 => '5',
		6 => '6',
		7 => '7',
		8 => '8',
		9 => '9',
		10 => '10',
		15 => '15',
		20 => '20',
		50 => '50',
		100 => '100'
	);
	public static $db = array(
		'NumPages' => 'Int',
		'SortOrder' => 'Varchar',
		'ExcludeHiddenPages' => 'Boolean'
	);
	static $defaults = array(
		'SortOrder' => 'Sort',
		'ExcludeHiddenPages' => '0'
	);
	public static $sort_options = array(
		'Sort' => 'Menu sort order',
		'Title' => 'Alfabetically by title'
	);
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Content.Settings', array(
				new LiteralField('SectionSettingHeader', '<br /><h3>'._t('Section.SETTINGSHEADER', 'Section Settings').'</h3>'),
				new DropdownField('SortOrder','Sort order of sub-pages',self::$sort_options),
				new DropdownField('NumPages','Number of sub-pages to list',self::$num_pages_options),
				new CheckboxField('ExcludeHiddenPages','Exclude pages hidden in the menu') 
			)
		);
		return $fields;
	}
}
class Section_Controller extends Page_Controller {
	public function ContentList() {
		$limit = '';
		if($this->NumPages) {
			if(!isset($_GET['start']) || !is_numeric($_GET['start']) || (int)$_GET['start'] < 1) $_GET['start'] = 0;
			$limit_start = (int)$_GET['start'];
			$limit = $limit_start.','.$this->NumPages;
		}
		$filter = 'ParentID = '. $this->ID;
		if($this->ExcludeHiddenPages) $filter .= ' AND ShowInMenus = 1';
		$data = DataObject::get('Page', $filter, $this->SortOrder,'',$limit);
		
		//hack avoiding DataObejctSet to set pageLength to 10 when it should be unlimited
		if($data && $data->pageLength == 0) {
			$data->pageLength = -1;
		}
		return $data;
	}
	public function IsFirstPage() {
		return ($this->request->getVar('start') == 0);
	}
}