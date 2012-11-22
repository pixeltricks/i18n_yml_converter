<?php
class GetLocalizationUploader {

	static $api_url = 'https://www.getlocalization.com/api/strings/';

	static $master_filename = 'en.yml';

	protected $project, $username, $password;
	
	/**
	 * @param  Project name on getlocalization.com
	 * @param [type] $username [description]
	 * @param [type] $password [description]
	 */
	function __construct($project, $username, $password) {
		$this->project = $project;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @param  Module name (usually a folder name on the filesystem)
	 */
	function run($module, $limitLangs = null) {
		$modules = SS_ClassLoader::instance()->getManifest()->getModules();
		$path = $modules[$module];
		$limitLangs = array('hr');
		$langs = array();
		$langFiles = new GlobIterator($path . '/lang/*.yml', FilesystemIterator::KEY_AS_FILENAME);
		foreach($langFiles as $langFile => $fileInfo) {
			$lang = preg_replace('/\.yml$/', '', $langFile);
			if($limitLangs && !in_array($lang, $limitLangs)) continue;
			
			$xml = $this->getXmlForLang($lang, $fileInfo);
			echo $xml;
			$this->submitXml($xml);
		}
		
	}

	/**
	 * @todo Doesn't work
	 * 
	 * @param  [type] $xml [description]
	 * @return [type]
	 */
	protected function submitXml($xml) {
		$ch = curl_init(self::$api_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', "Expect:"));              
		curl_setopt($ch, CURLOPT_HEADER, 1); 
		// curl_setopt($ch, CURLINFO_HEADER_OUT, 1);                                                                           
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);    
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);                                                            
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		$ret = curl_exec($ch);
		curl_close($ch);
	}

	protected function getXmlForLang($lang, $langFilePath) {
		if(!class_exists('sfYaml')) require_once 'thirdparty/sfYaml/lib/sfYaml.php';
		if(!class_exists('sfYamlParser')) require_once 'thirdparty/sfYaml/lib/sfYamlParser.php';
		if(!class_exists('sfYamlDumper')) require_once 'thirdparty/sfYaml/lib/sfYamlDumper.php';

		$template = <<<TPL
<Product>\$Product</Product>
<File>\$LangFile</File>
<Language>\$Lang</Language>
	<% control Strings %><GLString>
    <String><![CDATA[\$String]]></String>
    <LogicalString>\$LogicalString</LogicalString>
    <ContextInfo></ContextInfo>
    <MaxLength></MaxLength>
  </GLString><% end_control %>
TPL;

		$normalizedLang = str_replace('_', '-', $lang);
		$yml = sfYaml::load($langFilePath);
		$strings = new ArrayList();
		foreach($yml[$lang] as $ns => $entityNames) {
			// Assumes a one-level nesting only
			foreach($entityNames as $entityName => $entity) {
				$strings->push(new ArrayData(array(
					'String' => $entity,
					'LogicalString' => "{$ns}_{$entityName}"
				)));
			}
		}

		$obj = new ArrayData(array(
			'Lang' => $normalizedLang,
			'Product' => $this->project,
			// 'LangFile' => self::$master_filename,
			'LangFile' => basename($langFilePath),
			'Strings' => $strings
		));

		return SSViewer::fromString($template)->process($obj);
	}
}