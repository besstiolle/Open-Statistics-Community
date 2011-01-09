<?php


if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
}

function getConfiguration()
{
	$statistique = array(
				'cms_version' => array(),
				'installed_modules' => array(),
				'config_info' => array(),
				'php_information' => array(),
				'server_info' => array(),
				'permission_info' => array(),
				'network_info' => array()
				);
		
	clearstatcache();
	
	$statistique = conf_cmsms($statistique);
	$statistique = conf_module($statistique);
	$statistique = conf_info($statistique);
	$statistique = conf_php($statistique);
	$statistique = conf_serveur($statistique);
	$statistique = conf_permission($statistique);
	$statistique = conf_network($statistique);
	
	return $statistique;
}


function conf_cmsms($statistique){
	global $GLOBALS;
	$statistique['cms_version'] = $GLOBALS['CMS_VERSION'];	
	return $statistique;
}

function conf_module($statistique){
	global $gCms;
	$db = &$gCms->GetDb();
	$query = "SELECT module_name, status, version FROM ".cms_db_prefix()."modules WHERE active=1";
	$modules = $db->GetArray($query);
	$statistique['installed_modules'] = $modules;
	return $statistique;
}

function conf_info($statistique){
	$tmp = array();

	$tmp['php_memory_limit']['value']		= testConfig('php_memory_limit', 'php_memory_limit');
	$tmp['output_compression']['value']		= testConfig('output_compression', 'output_compression');
	$tmp['assume_mod_rewrite']['value']		= testConfig('assume_mod_rewrite', 'assume_mod_rewrite');
	$tmp['internal_pretty_urls']['value'] 	= testConfig('internal_pretty_urls', 'internal_pretty_urls');
	$tmp['use_hierarchy']['value'] 			= testConfig('use_hierarchy', 'use_hierarchy');

	$statistique['config_info'] = $tmp;
	return $statistique;
}

function conf_php($statistique){
	$tmp = array();
	
	$tmp['phpversion']['value'] 	= phpversion();
	$tmp['memory_limit']['value'] 	= ini_get('memory_limit');
	$tmp['safe_mode']['value']		= (ini_get('safe_mode')==1?'On':'Off');

	$statistique['php_information'] = $tmp;
	return $statistique;
}
	
function conf_serveur($statistique)
{
	global $config;
	$tmp = array();
	
	$tmp['server_api']['value']		= PHP_SAPI;
	
	
	switch($config['dbms']) //workaroud: ServerInfo() is unsupported in adodblite
	{
		case 'postgres7': $tmp['server_db_type']['value'] = 'PostgreSQL ('.$config['dbms'].')';
						$v = pg_version();
						$_server_db = (isset($v['server_version'])) ? $v['server_version'] : $v['client'];
						$tmp['server_db_version']['value'] = $_server_db;
						break;
		case 'mysqli':	$v = $db->connectionId->server_info;
		case 'mysql':	if(!isset($v)) $v = mysql_get_server_info();
						$tmp['server_db_type']['value'] = 'MySQL ('.$config['dbms'].')';
						$_server_db = (false === strpos($v, "-")) ? $v : substr($v, 0, strpos($v, "-"));
						$tmp['server_db_version']['value'] = $_server_db;
						break;
	}

	$statistique['server_info'] = $tmp;
	return $statistique;
}	

function conf_permission($statistique)
{
	$statistique['permission_info'] = array();
	return $statistique;
}

function conf_network($statistique)
{
	global $gCms;
	$oscs =& $gCms->modules["OpenStatisticsCommunity"]['object'];
	$myConnexion = $oscs->GetPreference("cryptageMethode");
	if($myConnexion == null)
	{
		$myConnexion = testConnexion($oscs,$smarty,new stdClass);
		$oscs->SetPreference("cryptageMethode", serialize($myConnexion));
	}
	$statistique['network_info'] = $myConnexion;
	return $statistique;

}


/**
 * @return object
 * @param string $title
 * @param string $varname
*/
function testConfig($title, $varname)
{
	global $gCms;
	$config = $gCms->config;

	if( (isset($config[$varname])) && (is_bool($config[$varname])) )
	{
		$value = (true == $config[$varname]) ? 'true' : 'false';
	}
	else if(! empty($config[$varname]))
	{
		$value = $config[$varname];
	}
	else
	{
		$value = '';
	}

	return $value;
}
?>