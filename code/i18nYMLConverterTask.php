<?php
/**
 * @package sapphire
 * @subpackage tasks
 */
class i18nYMLConverterTask extends BuildTask {
	
	protected $title = "i18n format converter task";
	
	protected $description = "";
	
	function init() {
		parent::init();
		
		$canAccess = (Director::isDev() || Director::is_cli() || Permission::check("ADMIN"));
		if(!$canAccess) return Security::permissionFailure($this);
	}
	
	/**
	 * This is the main method to build the master string tables with the original strings.
	 * It will search for existent modules that use the i18n feature, parse the _t() calls
	 * and write the resultant files in the lang folder of each module.
	 * 
	 * @uses DataObject->collectI18nStatics()
	 */	
	public function run($request) {
		increase_time_limit_to();
		$c = new i18nYMLConverter();
		$restrictModules = ($request->getVar('module')) ? explode(',', $request->getVar('module')) : null;
		return $c->run($restrictModules);
	}
}