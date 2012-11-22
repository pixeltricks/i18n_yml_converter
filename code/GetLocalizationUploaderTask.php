<?php
class GetLocalizationUploaderTask extends BuildTask {
	
	protected $title = "Uploads existing YML translations to getlocalization.com";
	
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
		$module = $request->getVar('module');
		if(!$module) throw new InvalidArgumentException('Needs "module" parameter');
		$project = $request->getVar('project');
		if(!$project) throw new InvalidArgumentException('Needs "project" parameter');
		$username = $request->getVar('username');
		if(!$username) throw new InvalidArgumentException('Needs "username" parameter');
		$password = $request->getVar('password');
		if(!$password) throw new InvalidArgumentException('Needs "password" parameter');

		increase_time_limit_to();
		$c = new GetLocalizationUploader($project, $username, $password);
		
		return $c->run($module);
	}

}