<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: systeminfo (basé sur le travail original de Ted Kulp.)
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://furie.be/shoutboxpro.html
#-------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
if (!isset($gCms)) exit;


// Vérification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Prefs')) {
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
}

define('CMS_BASE', dirname(dirname(dirname(__FILE__))));
require_once cms_join_path(CMS_BASE, 'include.php');
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');


global $gCms;
$db = &$gCms->GetDb();

$statistique = array('cms_version' => array(),'installed_modules' => array(),'config_info' => array(),'php_information' => array(),'server_info' => array(),'permission_info' => array());

/* CMS Install Information */

$statistique['cms_version'] = $GLOBALS['CMS_VERSION'];

$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE active=1";
$modules = $db->GetArray($query);

//Nettoyage du contenu
foreach ($modules as &$module)
{
	foreach($module as $key => $value)
	{
		if($key == "admin_only")
			unset($module[$key]);
		if($key == "active")
			unset($module[$key]);
	}
}
$statistique['installed_modules'] = $modules;

clearstatcache();
$tmp = array(0=>array(), 1=>array());

$tmp[0]['php_memory_limit'] = testConfig('php_memory_limit', 'php_memory_limit');
//$tmp[1]['process_whole_template'] = testConfig('process_whole_template', 'process_whole_template');
//$tmp[1]['debug'] = testConfig('debug', 'debug');
$tmp[0]['output_compression'] = testConfig('output_compression', 'output_compression');

//$tmp[1]['max_upload_size'] = testConfig('max_upload_size', 'max_upload_size');
//$tmp[1]['default_upload_permission'] = testConfig('default_upload_permission', 'default_upload_permission');
$tmp[0]['assume_mod_rewrite'] = testConfig('assume_mod_rewrite', 'assume_mod_rewrite');
//$tmp[1]['page_extension'] = testConfig('page_extension', 'page_extension');
$tmp[0]['internal_pretty_urls'] = testConfig('internal_pretty_urls', 'internal_pretty_urls');
$tmp[0]['use_hierarchy'] = testConfig('use_hierarchy', 'use_hierarchy');

//$tmp[1]['root_url'] = testConfig('root_url', 'root_url');
//$tmp[1]['root_path'] = testConfig('root_path', 'root_path', 'testDirWrite');
//$tmp[1]['previews_path'] = testConfig('previews_path', 'previews_path', 'testDirWrite');
//$tmp[1]['uploads_path'] = testConfig('uploads_path', 'uploads_path', 'testDirWrite');
//$tmp[1]['uploads_url'] = testConfig('uploads_url', 'uploads_url');
//$tmp[1]['image_uploads_path'] = testConfig('image_uploads_path', 'image_uploads_path', 'testDirWrite');
//$tmp[1]['image_uploads_url'] = testConfig('image_uploads_url', 'image_uploads_url');
//$tmp[1]['use_smarty_php_tags'] = testConfig('use_smarty_php_tags', 'use_smarty_php_tags');
//$tmp[1]['locale'] = testConfig('locale', 'locale');
//$tmp[1]['default_encoding'] = testConfig('default_encoding', 'default_encoding');
//$tmp[1]['admin_encoding'] = testConfig('admin_encoding', 'admin_encoding');

//Conservation que le premier niveau
$tmp = $tmp[0];

//Nettoyage du contenu
$tmpArray = array();
foreach ($tmp as $key => $object)
{
	$tmpArray[$key] = array('value' => $object->value);
}
$statistique['config_info'] = $tmpArray;




/* PHP Information */

$tmp = array(0=>array(), 1=>array());

$safe_mode = ini_get('safe_mode');
$session_save_path = ini_get('session.save_path');
$open_basedir = ini_get('open_basedir');


list($minimum, $recommended) = getTestValues('php_version');
$tmp[0]['phpversion'] = testVersionRange(0, 'phpversion', phpversion(), '', $minimum, $recommended, false);

//$tmp[1]['md5_function'] = testBoolean(0, 'md5_function', function_exists('md5'), '', false, false, 'Function_md5_disabled');

//list($minimum, $recommended) = getTestValues('gd_version');
//$tmp[1]['gd_version'] = testGDVersion(0, 'gd_version', $minimum, '', 'min_GD_version');

//$tmp[1]['tempnam_function'] = testBoolean(0, 'tempnam_function', function_exists('tempnam'), '', false, false, 'Function_tempnam_disabled');

//$tmp[1]['magic_quotes_runtime'] = testBoolean(0, 'magic_quotes_runtime', 'magic_quotes_runtime', lang('magic_quotes_runtime_on'), true, true, 'magic_quotes_runtime_On');

//$tmp[1]['create_dir_and_file'] = testCreateDirAndFile(0, '', '');


list($minimum, $recommended) = getTestValues('memory_limit');
$tmp[0]['memory_limit'] = testRange(0, 'memory_limit', 'memory_limit', '', $minimum, $recommended, true, true, null, 'memory_limit_range');
//list($minimum, $recommended) = getTestValues('max_execution_time');$tmp[0]['max_execution_time'] = testRange(0, 'max_execution_time', 'max_execution_time', '', $minimum, $recommended, true, false, 0, 'max_execution_time_range');
//$tmp[1]['register_globals'] = testBoolean(0, lang('register_globals'), 'register_globals', '', true, true, 'register_globals_enabled');
//$tmp[1]['output_buffering'] = testInteger(0, lang('output_buffering'), 'output_buffering', '', true, true, 'output_buffering_disabled');
//$tmp[1]['disable_functions'] = testString(0, lang('disable_functions'), 'disable_functions', '', true, 'green', 'yellow', 'disable_functions_not_empty');
$tmp[0]['safe_mode'] = testBoolean(0, 'safe_mode', 'safe_mode', '', true, true, 'safe_mode_enabled');
//$tmp[1]['open_basedir'] = testString(0, lang('open_basedir'), $open_basedir, '', false, 'green', 'yellow', 'open_basedir_enabled');
//$tmp[1]['test_remote_url'] = testRemoteFile(0, 'test_remote_url', '', lang('test_remote_url_failed'));
//$tmp[1]['file_uploads'] = testBoolean(0, 'file_uploads', 'file_uploads', '', true, false, 'Function_file_uploads_disabled');

//list($minimum, $recommended) = getTestValues('post_max_size');
//$tmp[1]['post_max_size'] = testRange(0, 'post_max_size', 'post_max_size', '', $minimum, $recommended, true, true, null, 'min_post_max_size');

//list($minimum, $recommended) = getTestValues('upload_max_filesize');
//$tmp[1]['upload_max_filesize'] = testRange(0, 'upload_max_filesize', 'upload_max_filesize', '', $minimum, $recommended, true, true, null, 'min_upload_max_filesize');

//$session_save_path = testSessionSavePath('');
//if(empty($session_save_path))
//{
//	$tmp[1]['session_save_path'] = testDummy('session_save_path', lang('os_session_save_path'), 'yellow', '', 'session_save_path_empty', '');
//}
//elseif (! empty($open_basedir))
//{
//	$tmp[1]['session_save_path'] = testDummy('session_save_path', lang('open_basedir_active'), 'yellow', '', 'No_check_session_save_path_with_open_basedir', '');
//}
//else
//{
//	$tmp[1]['session_save_path'] = testDirWrite(0, lang('session_save_path'), $session_save_path, $session_save_path, 1);
//}
//$tmp[1]['session_use_cookies'] = testBoolean(0, 'session.use_cookies', 'session.use_cookies');
//$tmp[1]['xml_function'] = testBoolean(1, 'xml_function', extension_loaded_or('xml'), '', false, false, 'Function_xml_disabled');
//$tmp[1]['file_get_contents'] = testBoolean(0, 'file_get_contents', function_exists('file_get_contents'), '', false, false, 'Function_file_get_content_disabled');

//$_log_errors_max_len = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0' : '99';
//ini_set('log_errors_max_len', $_log_errors_max_len);
//$result = (ini_get('log_errors_max_len') == $_log_errors_max_len);
//$tmp[1]['check_ini_set'] = testBoolean(0, 'check_ini_set', $result, lang('check_ini_set_off'), false, false, 'ini_set_disabled');

//Conservation que le premier niveau
$tmp = $tmp[0];

//Nettoyage du contenu
$tmpArray = array();
foreach ($tmp as $key => $object)
{
	$tmpArray[$key] = array('value' => $object->value);
}
$statistique['php_information'] = $tmpArray;




/* Server Information */

$tmp = array(0=>array(), 1=>array());

//$tmp[1]['server_software'] = testDummy('', $_SERVER['SERVER_SOFTWARE'], '');
$tmp[0]['server_api'] = testDummy('', PHP_SAPI, '');
//$tmp[1]['server_os'] = testDummy('', PHP_OS . ' ' . php_uname('r') .' '. lang('on') .' '. php_uname('m'), '');

switch($config['dbms']) //workaroud: ServerInfo() is unsupported in adodblite
{
	case 'postgres7': $tmp[0]['server_db_type'] = testDummy('', 'PostgreSQL ('.$config['dbms'].')', '');
					$v = pg_version();
					$_server_db = (isset($v['server_version'])) ? $v['server_version'] : $v['client'];
					list($minimum, $recommended) = getTestValues('pgsql_version');
					$tmp[0]['server_db_version'] = testVersionRange(0, 'server_db_version', $_server_db, '', $minimum, $recommended, false);
					break;
	case 'mysqli':	$v = $db->connectionId->server_info;
	case 'mysql':	if(!isset($v)) $v = mysql_get_server_info();
					$tmp[0]['server_db_type'] = testDummy('', 'MySQL ('.$config['dbms'].')', '');
					$_server_db = (false === strpos($v, "-")) ? $v : substr($v, 0, strpos($v, "-"));
					list($minimum, $recommended) = getTestValues('mysql_version');
					$tmp[0]['server_db_version'] = testVersionRange(0, 'server_db_version', $_server_db, '', $minimum, $recommended, false);
					break;
}

//Conservation que le premier niveau
$tmp = $tmp[0];

//Nettoyage du contenu
$tmpArray = array();
foreach ($tmp as $key => $object)
{
	$tmpArray[$key] = array('value' => $object->value);
}
$statistique['server_info'] = $tmpArray;

/* permission_info */

$tmp = array(0=>array(), 1=>array());

//$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'tmp';
//$tmp[1]['tmp'] = testDirWrite(0, $dir, $dir);

//$dir = TMP_TEMPLATES_C_LOCATION;
//$tmp[1]['templates_c'] = testDirWrite(0, $dir, $dir);

//$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'modules';
//$tmp[1]['modules'] = testDirWrite(0, $dir, $dir);

//$global_umask = get_site_preference('global_umask', '022');
//$tmp[1][lang('global_umask')] = testUmask(0, lang('global_umask'), $global_umask);

//$result = is_writable(CONFIG_FILE_LOCATION);
#$tmp[1]['config_file'] = testFileWritable(0, lang('config_writable'), CONFIG_FILE_LOCATION, '');
//$tmp[1]['config_file'] = testDummy('', substr(sprintf('%o', fileperms(CONFIG_FILE_LOCATION)), -4), (($result) ? 'red' : 'green'), (($result) ? lang('config_writable') : ''));

//Conservation que le premier niveau
$tmp = $tmp[0];

//Nettoyage du contenu
$tmpArray = array();
foreach ($tmp as $key => $object)
{
	$tmpArray[$key] = array('value' => $object->value);
}
$statistique['permission_info'] = $tmpArray;



?>
