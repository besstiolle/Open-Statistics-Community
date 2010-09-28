<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: Utilitaire de connexion réseau
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

// Vérification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Prefs')) {
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
}

function sendDatasFOpen($url,$data)
{
	$data = str_replace(array('+','#','&'), array('%2B','%23','%26'), $data);
		
	if(!ini_get('allow_url_fopen')) {
		return "#01";
	}
	
	$maxsize = 1000;
	$size = strlen($data);
	$nbpacket = ceil($size/$maxsize);
	
	//Récupération d'un Id de connexion
	$sid = '';
	$file = @fopen ("$url&new=$nbpacket", "r");
	if (!$file) {return "#02";}
	while (!feof ($file)) {$sid .= fgets ($file, 1024);}
	fclose($file);
	if($sid == "" || !is_numeric($sid)){echo "demande SID : $sid<br/>";return "#03";}
		
	//Connexions successives avec l'ID
	for ($i = 1; $i <= $nbpacket; $i++)
	{	
		$partdata = substr($data,$maxsize * ($i-1), $maxsize);
		$content = '';
		$file = @fopen ("$url&sid=$sid&packet=$i&partdata=$partdata", "r");
		if (!$file) {echo "toto";return "#04";}
		while (!feof ($file)) {$content .= fgets ($file, 1024);}
		fclose($file);
		if($content != "0"){echo $content;return "#05";}
	}
	
	return 0;
}


function sendDatasCURL($url,$data)
{
	$data = str_replace(array('+','#','&'), array('%2B','%23','%26'), $data);
	
	$maxsize = 1000;
	$size = strlen($data);
	$nbpacket = ceil($size/$maxsize);
	
	//Récupération d'un Id de connexion
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, "$url&new=$nbpacket");
	curl_setopt($curl, CURLOPT_HEADER, 0);
	$sid = curl_exec($curl);
	curl_close($curl);
	if($sid == "" || !is_numeric($sid)){echo "demande SID : $sid<br/>";return "#03";}
	
	//Connexions successives avec l'ID
	for ($i = 1; $i <= $nbpacket; $i++)
	{	
		$partdata = substr($data,$maxsize * ($i-1), $maxsize);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, "$url&sid=$sid&packet=$i&partdata=$partdata");
		curl_setopt($curl, CURLOPT_HEADER, 0);
		$content = curl_exec($curl);
		curl_close($curl);
		if($content != "0"){echo $content;return "#05";}
	}
	
	return 0;
}

function sendDatasFGC($url,$data)
{
	$data = str_replace(array('+','#','&'), array('%2B','%23','%26'), $data);
	
	$maxsize = 1000;
	$size = strlen($data);
	$nbpacket = ceil($size/$maxsize);
	
	//Récupération d'un Id de connexion
	$sid = file_get_contents("$url&new=$nbpacket");
	if($sid == "" || !is_numeric($sid)){echo "demande SID : $sid<br/>";return "#03";}
		
	//Connexions successives avec l'ID
	for ($i = 1; $i <= $nbpacket; $i++)
	{	
		$partdata = substr($data,$maxsize * ($i-1), $maxsize);
		$content = file_get_contents("$url&sid=$sid&packet=$i&partdata=$partdata");
		if($content != "0"){echo $content;return "#05";}
	}
	
	return 0;
}

function testConnexion($module,$smarty,$myConnexion)
{
	$hasDefault = false;
	
	$urlBase = $module->GetPreference("cryptageUrl");
	$urlBase = $module->_estLocalhost($urlBase);

	$smarty->assign('serveur', $urlBase);

	$urlComplement = "/modules/OpenStatisticsCommunityServer/testReseau.php";
	$urlTest = $urlBase.$urlComplement;

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
				$hasDefault = true;
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
				$myConnexion->curl->usable = true;
				if(!$hasDefault)
				{	
					$myConnexion->curl->defaut = true;
					$hasDefault = true;
				}
			}
		} 
	}
	
	
	/** Test du mode de connexion file_get_contents **/
	$myConnexion->fileGetContent = new stdClass;
	$myConnexion->fileGetContent->actif = false;
	$myConnexion->fileGetContent->usable = false;
	$myConnexion->fileGetContent->defaut = false;
	$content = @file_get_contents($urlTest);
	$myConnexion->fileGetContent->actif = true;
	if($content != "")
	{
		$myConnexion->fileGetContent->usable = true;
		if(!$hasDefault)
		{	
			$myConnexion->fileGetContent->defaut = true;
			$hasDefault = true;
		}
	} 
	
	

	/** Test du mode de connexion fsockopen **/
	$myConnexion->fsockopen = new stdClass;
	$myConnexion->fsockopen->actif = false;
	$myConnexion->fsockopen->usable = false;
	$myConnexion->fsockopen->defaut = false;
	
	
	//TODO : poursuivre le code avec fsockopen
	/*
	$file = @fsockopen("cmsmadesimple.fr", 80);
	$content = "";
	if ($file) {
		$myConnexion->fsockopen->actif = true;
		stream_set_timeout($file,2);        
		fputs($file, "GET /$urlComplement HTTP/1.0\r\n\r\n");		
		while (!feof ($file)) {$content .= fgets ($file, 1024);}
		fclose($file);
		if($content != "")
		{
			$myConnexion->fsockopen->usable = true;
			if(!$hasDefault)
			{	
				$myConnexion->fsockopen->defaut = true;
				$hasDefault = true;
			}
			die($content);
		} 
	} else
	{
		die("Impossible d'ouvrir\n" . substr($urlBase,7));
	}*/
	
	
	
	/** Test du mode de connexion image simple **/
	/*$myConnexion->img = new stdClass;
	$myConnexion->img->url = "$urlTest&img=simple";
	$myConnexion->img->urlrep = "$urlTest&img=retour";*/
	
	return $myConnexion;
}

?>