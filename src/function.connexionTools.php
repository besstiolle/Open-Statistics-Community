<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: 
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
//		echo $i." <br/>"; 
		$partdata = substr($data,$maxsize * ($i-1), $maxsize);
//		echo ($maxsize * ($i-1)).'<br/>';
//		echo $partdata.'<br/>';
		$content = '';
		$file = @fopen ("$url&sid=$sid&packet=$i&partdata=$partdata", "r");
//		echo "$url&sid=$sid&packet=$i&partdata=$partdata\n";
		if (!$file) {echo "toto";return "#04";}
		while (!feof ($file)) {$content .= fgets ($file, 1024);}
		fclose($file);
		if($content != "0"){echo $content;return "#05";}
	}
	
	return 0;
}

?>