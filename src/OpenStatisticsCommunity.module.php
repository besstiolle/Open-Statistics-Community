<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: OpenStatisticsCommunity.module.class
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
 
class OpenStatisticsCommunity extends CMSModule
{
  function GetName()
  {
    return get_class($this);
  }
  
  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

  function GetVersion()
  {
    return '0.1.6-beta1';
  }
  
  function GetHelp()
  {
    return $this->Lang('help');
  }
  
  function GetAuthor()
  {
    return 'Kevin Danezis (Bess)';
  }

  function GetAuthorEmail()
  {
    return 'besstiolle@gmail.com';
  }
  
  function GetChangeLog()
  {
    return $this->Lang('changelog');
  }
  
  function IsPluginModule()
  {
    return false;
  }

  function HasAdmin()
  {
    return true;
  }

  function GetAdminSection()
  {
    return 'extensions';
  }

  function GetAdminDescription()
  {
    return $this->Lang('moddescription');
  }

  function VisibleToAdminUser()
  {
    return $this->CheckPermission('Set Open Statistics Community Prefs');
  }
  
  function GetDependencies()
  {
    return array();
  }

  function MinimumCMSVersion()
  {
    return "1.5";
  }
  
   function MaximumCMSVersion()
  {
    return "1.9";
  }
  
  /**
   * SetParameters()
   */ 
  function SetParameters()
  {
	$this->RestrictUnknownParams();
  }
  
	/**
	 * A vrai specifie que la classe possede un appel a evenement
	 */
	function HandlesEvents()
	{
		return true;
	}

	function DoEvent($originator, $eventname, &$params)
	{
		global $gCms;
		global $smarty;
		
		if($eventname == "LoginPost" )
		{
			//Verification que la connexion s'est bien deroulee
			if(!$this->CheckPermission('Set Open Statistics Community Prefs'))
				return;
				
			//Si aucune autorisation n'a ete definie on est dans le cas d'une non-config = envoi
			$autorisations = $this->GetPreference('autorisations');
			if(isset($autorisations))
			{
				$autorisations = unserialize($autorisations);
				//Si autorisation definie mais qu'on bloque les envois : on annule
				if(!isset($autorisations['all']) || !$autorisations['all'])
					return;
			}
				
			//On n'envois de rapport que tous les 10 jours
			$db = &$gCms->GetDb();
			$maxdate = $db->GetOne('SELECT max(osc_date_envoi) from '.cms_db_prefix().'module_openstatisticscommunity_historique');
			//Si ecart > 10 jours
			if ((time() - $this->_dbToDate($maxdate)) < (86400*10))
			{
				return;
			}
		}
		
		include_once(dirname(__FILE__).'/function.sendReport.php');
	}
  
  /**
   * In the Admin Area, there are notices which can be displayed to the admin user.
   * These tend to be for high-priority notices, like version upgrades, security issues,
   * un-configured modules, etc.
   * These notices are assigned a priority from 1 to 3, with 1 being the highest.
   * (priority filtering is not yet supported, but will be).
   * Notices should be used very sparingly, as if there are too many alerts, the user
   * will stop paying attention.
   * That being said, this example will post an alert until someone adds a records
   * using the Skeleton Module.
   * 
   * @returns a stdClass object with two properties.... priority (1->3)... and
   * html, which indicates the text to display for the Notification.
   */
  function GetNotificationOutput($priority=2) 
  {
	global $gCms;
	$db = &$gCms->GetDb();
	$rcount = $db->GetOne('SELECT osc_reponse FROM '.cms_db_prefix().'module_openstatisticscommunity_historique order by osc_date_envoi desc limit 0,1');
	
	if ($priority < 4 && $rcount != '0' )
    {
		$ret = new stdClass;
		$ret->priority = 2;
		$ret->html=$this->Lang('alert_not_send');
		return $ret;
    }  
	return '';
  }

  //PENSER A : get_module_path()
  
  /**
   * InstallPostMessage()
   * @return string Message to be shown after installation
   */
  function InstallPostMessage()
  {
    return $this->Lang('postinstall',$this->GetVersion());
  }

  /**
   * UninstallPostMessage()
   * @return string Message to be shown after uninstallation
   */
  function UninstallPostMessage()
  {
    return $this->Lang('postuninstall');
  }

  /**
   * UninstallPreMessage()
   * @return string Message to be shown before uninstallation
   */
  function UninstallPreMessage()
  {
    return $this->Lang('really_uninstall');
  }
  
	/**
	* Transforme la date issue de la base en une veritable date php
	**/
	function _dbToDate($stringDate)
	{
		return mktime(substr($stringDate, 11,2),
					substr($stringDate, 14,2),
					substr($stringDate, 17,2),
					substr($stringDate, 5,2),
					substr($stringDate, 8,2),
					substr($stringDate, 0,4));
	}
	
	function _getTimeForDB($db)
	{
		return trim($db->DBTimeStamp(time()), "'");
	}
	
	function _GenerationCle($Texte,$CleDEncryptage)
	  {
	  $CleDEncryptage = md5($CleDEncryptage);
	  $Compteur=0;
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		if ($Compteur==strlen($CleDEncryptage))
		  $Compteur=0;
		$VariableTemp.= substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1);
		$Compteur++;
		}
	  return $VariableTemp;
	  }

	function _Crypte($Texte,$Cle)
	{
	  srand((double)microtime()*1000000);
	  $CleDEncryptage = md5(rand(0,32000) );
	  $Compteur=0;
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		if ($Compteur==strlen($CleDEncryptage))
		  $Compteur=0;
		$VariableTemp.= substr($CleDEncryptage,$Compteur,1).(substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1) );
		$Compteur++;
		}
		
	  return base64_encode($this->_GenerationCle($VariableTemp,$Cle) );
    }
  
	function _getCodes()
	{
		return array(
		0 => "Rapport transmis avec succ&egrave;s",
		100 => "Identification KO",
		200 => "CNI incorrecte ou inconnue",
		201 => "Corruption des donn&eacute;es transf&eacute;r&eacute;es",
		202 => "Tentative d'usurpation d'identit&eacute;",
		203 => "Taille 20ko exc&eacute;d&eacute;e",
		204 => "Data not found",
		500 => "Serveur indisponible pour une dur&eacute;e indetermin&eacute;e",
		501 => "Serveur indisponible pour une courte dur&eacute;e",
		503 => "Erreur interne innatendue"
		);
	}
	
	function _estLocalhost($urlBase, $urlRepertoire)
	{
		$file = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'localhost';
		
		if(file_exists($file))
		{
			//return array("http://localhost","/cms7-notation");
			return array("http://localhost","/skeleton");
		} 
		
		return array($urlBase, $urlRepertoire);
	}
	
	function _debug()
	{
		global $gCms;
		$db = &$gCms->GetDb();
		$result = $db->Execute('SELECT sitepref_name as name, sitepref_value as value  FROM '.cms_db_prefix().'siteprefs WHERE sitepref_name LIKE \'OpenStatisticsCommunity_%\'');
		if ($result === false)
		{
			echo "Database error!";
			exit;
		}
		
		
		echo "<table>\n";
		while ($row = $result->FetchRow())
		{
			if(@is_array(@unserialize($row['value'])) || @is_object(@unserialize($row['value'])))
			{
				$val = print_r(unserialize($row['value']), true);
				echo "<tr><td>".$row['name']." </td><td>".$val."</td></tr>\n";
			}
			else
			{
				echo "<tr><td>".$row['name']." </td><td>".$row['value']."</td></tr>\n";
			}
		}
		echo "</table>\n";
	}
}
?>