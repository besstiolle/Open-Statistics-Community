<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: envoi des rapports
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
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
}

$db =& $gCms->GetDb();
$osc =& $gCms->modules["OpenStatisticsCommunity"]['object'];

//Verification des directives utilisateurs
$autorisations = unserialize($this->GetPreference('autorisations'));
if(!isset($autorisations['all']) || !$autorisations['all'])
{
	$smarty->assign("message","<p>Vous avez d&eacute;sactiv&eacute; les envois ! R&eacute;activez les dans la partie configuration de la confidentialit&eacute;</p>");
	return;
}

require_once(dirname(__FILE__).'/function.connexionTools.php');
require_once(dirname(__FILE__).'/function.configurationTools.php');
$statistique = getConfiguration();

//On ne garde que les parties explicitement demandees.
if(!isset($autorisations['cms_version']) || !$autorisations['cms_version'])
{
	unset($statistique['cms_version']);
}

$i=0;
foreach($statistique['installed_modules'] as $element)
{
	if(!isset($autorisations[$element['module_name']]) || !$autorisations[$element['module_name']])
	{
		unset($statistique['installed_modules'][$i]);	
	}
	$i++;
}
//Reindex du tableau
$statistique['installed_modules'] = array_values($statistique['installed_modules']);

foreach($statistique['config_info'] as $key=>$element)
{	
	if(!isset($autorisations[$key]) || !$autorisations[$key])
	{
		unset($statistique['config_info'][$key]);	
	}
}
foreach($statistique['php_information'] as $key=>$element)
{	
	if(!isset($autorisations[$key]) || !$autorisations[$key])
	{
		unset($statistique['php_information'][$key]);	
	}
}
foreach($statistique['server_info'] as $key=>$element)
{	
	if(!isset($autorisations[$key]) || !$autorisations[$key])
	{
		unset($statistique['server_info'][$key]);	
	}		
}
if(isset($statistique['network_info']['fct_reseau']) && (!isset($autorisations["fct_reseau"]) || !$autorisations["fct_reseau"]))
{
	unset($statistique['network_info']['fct_reseau']);	
}	

$myConnexion = unserialize($this->GetPreference("cryptageMethode"));
if(!$myConnexion->hasDefault){
	$smarty->assign("message",$this->Lang('no_connexion_allow'));
	return;
}

$cle = $this->GetPreference("cryptageCle");
$CNI = $this->GetPreference("cryptageCNI");

$urlBase = $this->GetPreference("cryptageUrl_Base");
$urlRepertoire = $this->GetPreference("cryptageUrl_Repertoire");
$retour =  $this->_estLocalhost($urlBase, $urlRepertoire);
$urlBase = $retour[0];
$urlRepertoire = $retour[1];
$urlRepertoire .= "/modules/OpenStatisticsCommunityServer";


//Necessite de recuperer une nouvelle CNI
if(!isset($cle) || empty($cle) || !isset($CNI) || empty($CNI))
{
	$urlComplementaire = "/ajax.askCNI.php";
	$content = call($myConnexion,$urlBase, $urlRepertoire.$urlComplementaire);
	
	if("#02" == $content)
	{
		$smarty->assign("message","<p>Impossible de lire la page.</p>");
		return;
	} 
	
	$out = preg_split("/\|/", $content);
	$CNI = $out[0];
	$cle = $out[1];
	
	if(isset($cle) && isset($CNI) && strlen($cle) == 50 && strlen($CNI) == 50)
	{
		$this->SetPreference("cryptageCle", $cle);
		$this->SetPreference("cryptageCNI", $CNI);
	} else
	{
		$smarty->assign("message","Mauvaise r&eacute;ponse du serveur : $content");
		makelog($db, $osc, "askCNI ko" , "manuel");
		return;
	}
	

}

$data = $this->_Crypte(serialize($statistique), $cle);
$size = strlen($data);
$resume = md5($data);

//Remplacement des 3 caracteres foireux
$data = str_replace(array('+','#','&'), array('%2B','%23','%26'), $data);

$urlComplementaire = "/ajax.saveResponseMulti.php?CNI=%s&RESUME=%s&SIZE=%s";
$urlComplementaire = sprintf($urlComplementaire, $CNI, $resume, $size);



	$maxsize = 1000;
	$size = strlen($data);
	$nbpacket = ceil($size/$maxsize);
	$codeRetour = "0";
	
	//Recuperation d'un Id de connexion
	$sid = call($myConnexion,$urlBase, $urlRepertoire.$urlComplementaire."&new=".$nbpacket);
	if($sid == "" || !is_numeric($sid))
	{
		echo "demande SID KO : $sid<br/>\n";
		$codeRetour = "#03";
	}
	else
	{	
		//Connexions successives avec l'ID
		for ($i = 1; $i <= $nbpacket; $i++)
		{	
			$partdata = substr($data,$maxsize * ($i-1), $maxsize);
			$content = call($myConnexion,$urlBase, $urlRepertoire.$urlComplementaire."&sid=$sid&packet=$i&partdata=$partdata");
			if($content != "0")
			{
				echo "reponse serveur KO : $content<br/>\n";
				$codeRetour = "#05";
			}
		}
	}


if($codeRetour == "0")
{
	$smarty->assign("message", "Envoi r&eacute;ussi :)");
} else
{
	$smarty->assign("message", "erreur durant l'envoi : $codeRetour");
}

makelog($db, $osc, $codeRetour , "manuel");

function makelog($db, $osc, $codeRetour , $handler)
{

	//on enregistre en base le succes
	$queryInsert = 'INSERT INTO '.cms_db_prefix().'module_openstatisticscommunity_historique (osc_id, osc_reponse, osc_handler, osc_date_envoi) values (?,?,?,?)';

	$sid = $db->GenID(cms_db_prefix().'module_openstatisticscommunity_historique_seq');
	$time = $osc->_getTimeForDB($db);

	$param = array($sid, $codeRetour , $handler,  $time);
	$result = $db->Execute($queryInsert, $param);

	if ($result === false){die("Database error durant l'insert!");}

}

?>