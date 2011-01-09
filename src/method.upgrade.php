<?php
if (!isset($gCms)) exit;

$db =& $gCms->GetDb();
$osc =& $gCms->modules["OpenStatisticsCommunity"]['object'];

if(!isset($osc))
	return;

$current_version = $oldversion;

switch($current_version)
{
  case '0.0.1':
  case '0.0.2':
  case '0.0.3':

	// 	Installation d'un module
	$osc->AddEventHandler('core','ModuleInstalled',true);
	// 	Desinstallation d'un module
	$osc->AddEventHandler('core','ModuleUninstalled',true);
	// 	Mise a jour d'un module
	$osc->AddEventHandler('core','ModuleUpgraded',true);
	// 	Login (sous reserve d'une inaction depuis 10 jours)
	$osc->AddEventHandler('core','LoginPost',true);
	
  case '0.0.4':
  case '0.0.5':
  case '0.0.6-beta1':
  case '0.0.6-beta2':
  case '0.0.6-beta3':
  
  	$osc->RemovePreference("cryptageTmp");
	$osc->RemovePreference("cryptageUrl");
	$osc->SetPreference("cryptageUrl_Base", "http://www.cmsmadesimple.fr");
	$osc->SetPreference("cryptageUrl_Repertoire", "");
  
  case '0.0.6':
  
}

//On lance les tests de reseau
require_once(dirname(__FILE__).'/function.connexionTools.php');
$osc->SetPreference("cryptageMethode", "");
$myConnexion = testConnexion($osc,$smarty,new stdClass);
$osc->SetPreference("cryptageMethode", serialize($myConnexion));
?>