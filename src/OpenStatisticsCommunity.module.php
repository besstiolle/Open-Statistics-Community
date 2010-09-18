<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: OpenStatisticsCommunity.module.class
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
 
class OpenStatisticsCommunity extends CMSModule
{
  
  /**
   * GetName()
   * @return string class name
   */
  function GetName()
  {
    return get_class($this);
  }
  
  /**
   * GetFriendlyName()
   * @return string Friendly name for the module
   */
  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

  
  /**
   * GetVersion()
   * @return string version number 
   */
  function GetVersion()
  {
    return '0.1.1';
  }
  
  /**
   * GetHelp()
   * @return string Help for this module
   */
  function GetHelp()
  {
    return $this->Lang('help');
  }
  
  /**
   * GetAuthor()
   * @return string Author name
   */
  function GetAuthor()
  {
    return 'Kevin Danezis (Bess)';
  }

  /**
   * GetAuthorEmail()
   * @return string Authors email
   */
  function GetAuthorEmail()
  {
    return 'besstiolle@gmail.com';
  }
  
  /**
   * GetChangeLog()
   * @return string ChangeLog for this module
   */
  function GetChangeLog()
  {
    return $this->Lang('changelog');
  }
  
  /**
   * IsPluginModule()
   * @return bool True if this module can be included in page and or template
   */
  function IsPluginModule()
  {
    return false;
  }

  /**
   * HasAdmin()
   * @return bool True if this module has admin area
   */
  function HasAdmin()
  {
    return true;
  }

  /**
   * GetAdminSection()
   * @return string Which admin section this module belongs to
   */
  function GetAdminSection()
  {
    return 'extensions';
  }

  /**
   * GetAdminDescription()
   * @return string Module description
   */
  function GetAdminDescription()
  {
    return $this->Lang('moddescription');
  }

  /**
   * VisibleToAdminUser()
   * @return bool True if this module is shown to current user
   */
  function VisibleToAdminUser()
  {
    return true;
  }
  
  /**
   * GetDependencies()
   * @return hash Hash of other modules this module depends on
   */
  function GetDependencies()
  {
    return array();
  }

  /**
   * MinimumCMSVersion()
   * @return string Minimum cms version this module should work on
   */
  function MinimumCMSVersion()
  {
    return "1.5";
  }
  
  /**
   * SetParameters()
   */ 
  function SetParameters()
  {
	//$this->RegisterModulePlugin();
	
	$this->RestrictUnknownParams();
	
  }
  
	/**
	 * A vrai spécifie que la classe possède un appel à évenement
	 */
	function HandlesEvents()
	{
		return true;
	}

	function DoEvent($originator, $eventname, &$params)
	{
		global $gCms;
		global $smarty;
		
		if($eventname == "LoginPost")
		{
			//On n'envois de rapport que tous les 10 jours
			//TODO;
			return;
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
 /* function GetNotificationOutput($priority=2) 
  {
	global $gCms;
	$db = &$gCms->GetDb();
	$rcount = $db->GetOne('select count(*) from '.cms_db_prefix().'module_skeleton');
    if ($priority < 4 && $rcount == 0 )
      {
	  $ret = new stdClass;
	  $ret->priority = 2;
	  $ret->html=$this->Lang('alert_no_records');
	  return $ret;
      }  
	return '';
  }*/

  //PENSER A : get_module_path()
  
  /**
   * GetEventDescription()
   * @param string Eventname
   * @return string Description for event 
   */
 /* function GetEventDescription ( $eventname )
  {
    return $this->Lang('event_info_shootbox'.$eventname );
  }*/
  
  /**
   * GetEventHelp()
   * @param string Eventname
   * @return string Help for event
   */
/*  function GetEventHelp ( $eventname )
  {
    return $this->Lang('event_help_shootbox'.$eventname );
  }*/

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
	* Transforme la date issue de la base en une véritable date php
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
		503 => "Erreur interne inatendue"
		);
	}
	
	function _estLocalhost($urlTest)
	{
		$file = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'localhost';
		
		if(file_exists($file))
		{
			//return  "http://localhost/cms7-notation";
			return "http://localhost/skeleton";
		} 
		
		return $urlTest;
		
	}
}
?>
