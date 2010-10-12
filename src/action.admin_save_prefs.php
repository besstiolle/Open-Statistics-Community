<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: action.save_pref
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

$autorisations = unserialize($this->GetPreference('autorisations'));
foreach($autorisations as $key=>$value)
{
	$autorisations[$key] = (isset($_POST['m1_'.$key])?true:false);
}
$this->SetPreference('autorisations',serialize($autorisations));


// write to the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('prefsupdated') );

// redirect back to default admin page
$this->Redirect($id, 'defaultadmin', $returnid, array('tab_message'=> 'prefsupdated', 'active_tab' => 'configuration'));
?>