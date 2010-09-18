<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: action.save_pref
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://www.cmsmadesimple.fr/forum/viewtopic.php?id=2908
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

$this->SetPreference("cryptageMethode", "");

$myConnexion = new stdClass;
$urlTest = $this->GetPreference("cryptageUrl");

$urlTest = $this->_estLocalhost($urlTest);

$smarty->assign('serveur', $urlTest);


$urlTest .= "/modules/OpenStatisticsCommunityServer/testReseau.php";

/** Test du mode de connexion Fopen **/
$myConnexion->fopen = new stdClass;
$myConnexion->fopen->actif = false;
$myConnexion->fopen->usable = false;
$myConnexion->fopen->defaut = false;
if(@ini_get('allow_url_fopen')) {
	$myConnexion->fopen->actif = true;
	$file = @fopen ($urlTest, "r");
	$content = "";
	if ($file) {
		while (!feof ($file)) {$content .= fgets ($file, 1024);}
		fclose($file);
		if($content != "")
		{
			$myConnexion->fopen->usable = true;
			$myConnexion->fopen->defaut = true;
		} 
	}
} 


/** Test du mode de connexion cUrl **/
$myConnexion->curl = new stdClass;
$myConnexion->curl->actif = false;
$myConnexion->curl->usable = false;
$myConnexion->curl->defaut = false;

if(function_exists('curl_init')) {
	$myConnexion->curl->actif = true;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $urlTest);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	$content = curl_exec($curl);
	curl_close($curl);
	if($content != "")
	{
		$myConnexion->curl->out = true;
		if($content == "0");
		{
			$myConnexion->curl->in = true;
			$myConnexion->curl->usable = true;
			if(!$myConnexion->fopen->defaut)
			{	
				$myConnexion->curl->defaut = true;
			}
		}
	} 
}

/** Test du mode de connexion image simple **/
/*$myConnexion->img = new stdClass;
$myConnexion->img->url = "$urlTest&img=simple";
$myConnexion->img->urlrep = "$urlTest&img=retour";*/


$this->SetPreference("cryptageMethode", serialize($myConnexion));


$admintheme =& $gCms->variables['admintheme'];

$backlink = $this->CreateLink($id, 'defaultadmin', $returnid, $admintheme->DisplayImage('icons/system/back.gif', $this->Lang('back'),'','','systemicon'));
$smarty->assign('backlink', $backlink);
$smarty->assign('myConnexion', unserialize($this->GetPreference("cryptageMethode")));
$smarty->assign('img_true', $admintheme->DisplayImage('icons/system/true.gif', '','','','systemicon'));
$smarty->assign('img_false', $admintheme->DisplayImage('icons/system/false.gif', '','','','systemicon'));

echo $this->ProcessTemplate('adminTestReseau.tpl');
?>