<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: admin_configurationtab.class
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://www.cmsmadesimple.fr/forum/viewtopic.php?id=2908
# The module's forge id : http://dev.cmsmadesimple.org/projects/osc
# The statistiques homepage is: http://www.cmsmadesimple.fr/statistiques
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

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Prefs')) {
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
}

if(isset($params['eraseCNI']))
{
	$this->SetPreference("cryptageCNI", null);
	$myCni = null;
}
else
{
	$myCni = $this->GetPreference("cryptageCNI");
}

$autorisations = unserialize($this->GetPreference('autorisations'));
$newAut = array();

$master = new stdclass;
$ssligneid	  		= 'all';
$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
$master->input 		= $this->CreateInputCheckbox($id,$ssligneid,true,$newAut[$ssligneid], "id='m1_all'");
$master->text  		= $this->Lang('allow_send_report');
$master->listeLigne	= array();

	//Version de l'install
	$ligne = new stdclass;
	$ligne->text  		= $this->Lang('allow_send_cms_version');
	$ligne->sslisteLigne	= array();

		$ssligne = new stdclass;
		$ssligneid	  		= 'cms_version';
		$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
		$ssligne->input 	= $this->CreateInputCheckbox($id,$ssligneid,true,($newAut[$ssligneid]));
		$ssligne->text  	= $ssligneid.":".$statistique['cms_version'];
				
	$ligne->sslisteLigne[] = $ssligne;

$master->listeLigne[]	= $ligne;
	
	
	//Modules
	$ligne = new stdclass;
	$ligne->text  			= $this->Lang('allow_send_module_version');
	$ligne->sslisteLigne	= array();

	foreach($statistique['installed_modules'] as $element)
	{	
		$ssligne = new stdclass;
		$ssligneid	  		= $element['module_name'];
		$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
		$ssligne->input 	= $this->CreateInputCheckbox($id,$ssligneid,true,($newAut[$ssligneid]));
		$ssligne->text  	= $element['module_name'].":".$element['version'];
		
		$ligne->sslisteLigne[] = $ssligne;
	}

$master->listeLigne[]	= $ligne;
	
	//Config cms
	$ligne = new stdclass;
	$ligne->text  			= $this->Lang('allow_send_config_information');
	$ligne->sslisteLigne	= array();
	
	foreach($statistique['config_info'] as $key=>$element)
	{	
		$ssligne = new stdclass;
		$ssligneid	  		= $key;
		$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
		$ssligne->input 	= $this->CreateInputCheckbox($id,$ssligneid,true,($newAut[$ssligneid]));
		$ssligne->text  	= $key.":".$element['value'];
		
		$ligne->sslisteLigne[] = $ssligne;
	}
	
$master->listeLigne[]	= $ligne;	
	
	//Config php
	$ligne = new stdclass;
	$ligne->text  			= $this->Lang('allow_send_php_information');
	$ligne->sslisteLigne	= array();
	
	foreach($statistique['php_information'] as $key=>$element)
	{	
		$ssligne = new stdclass;
		$ssligneid	  		= $key;
		$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
		$ssligne->input 	= $this->CreateInputCheckbox($id,$ssligneid,true,($newAut[$ssligneid]));
		$ssligne->text  	= $key.":".$element['value'];
		
		$ligne->sslisteLigne[] = $ssligne;
	}
	
$master->listeLigne[]	= $ligne;	
	
	//Config php
	$ligne = new stdclass;
	$ligne->text  			= $this->Lang('allow_send_server_information');
	$ligne->sslisteLigne	= array();
	
	foreach($statistique['server_info'] as $key=>$element)
	{	
		$ssligne = new stdclass;
		$ssligneid	  		= $key;
		$newAut[$ssligneid]	= (!isset($autorisations[$ssligneid])?true:$autorisations[$ssligneid]);
		$ssligne->input 	= $this->CreateInputCheckbox($id,$ssligneid,true,($newAut[$ssligneid]));
		$ssligne->text  	= $key.":".$element['value'];
		
		$ligne->sslisteLigne[] = $ssligne;
	}
	
$master->listeLigne[]	= $ligne;	

// assign des valeurs pour le frontal des stats
$smarty->assign('cni',($myCni!=null?$myCni:$this->Lang('noCniDefined')));
$smarty->assign('master', $master);

$this->SetPreference('autorisations',serialize($newAut));






//Bouton de reset du CNI
$resetlink =($myCni == null?"":$this->CreateLink($id, 'defaultadmin', $returnid, 'Cliquez ici pour r&eacute;initialiser la CNI',array('eraseCNI'=>true)));

//Bouton de controle reseau
$admintheme =& $gCms->variables['admintheme'];
$reseaulink = $this->CreateLink($id, 'admin_test_reseau', $returnid, $admintheme->DisplayImage('icons/system/info.gif', $this->Lang('test_reseau'),'','','systemicon'));




$smarty->assign('resetlink', $resetlink);
$smarty->assign('reseaulink', $reseaulink);

$default_connexion = "aucune connexion trouv&eacute;e";
$myConnexion = unserialize($this->GetPreference("cryptageMethode"));
$error_connexion = false;
if($myConnexion == null)
{
	//On lance les tests de reseau
	require_once(dirname(__FILE__).'/function.connexionTools.php');
	$this->SetPreference("cryptageMethode", "");
	$myConnexion = testConnexion($this,$smarty,new stdClass);
	$this->SetPreference("cryptageMethode", serialize($myConnexion));
}

if($myConnexion->fopen->defaut)
{
	$default_connexion = "Connexion par fopen() par d&eacute;faut ".$admintheme->DisplayImage('icons/system/true.gif');
} elseif($myConnexion->curl->defaut)
{
	$default_connexion = "Connexion par cUrl() par d&eacute;faut ".$admintheme->DisplayImage('icons/system/true.gif');
} elseif($myConnexion->fileGetContent->defaut)
{
	$default_connexion = "Connexion par file_get_content() par d&eacute;faut ".$admintheme->DisplayImage('icons/system/true.gif');
} elseif($myConnexion->fsockopen->defaut)
{
	$default_connexion = "Connexion par fsockopen() par d&eacute;faut ".$admintheme->DisplayImage('icons/system/true.gif');
}else
{
	$default_connexion = "Aucune connexion disponible actuellement. Vous pouvez relancer un test de configuration afin de rafraichir les r&eacute;sultats.";
	$error_connexion = true;
}


//Test des connexions sortantes
$smarty->assign('error_connexion', $error_connexion);
$smarty->assign('default_connexion', $default_connexion);

?>