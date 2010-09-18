<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client l�g� envoyant toute une s�rie de 
#         statistiques de mani�re anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: b�ta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: admin_historiquetab.class
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


// V�rification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Prefs')) {
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
}

// Get the historique
$query = 'SELECT * FROM '.cms_db_prefix().'module_openstatisticscommunity_historique ORDER BY osc_date_envoi DESC';
$result = $db->Execute($query);
if ($result === false)
{
	echo "Database error!";
	exit;
}


$listeFrontal = array();

$i = 0;
$codes = $this->_getCodes();
while ($row = $result->FetchRow())
{
	$obj = new stdClass;
	$obj->id = $row['osc_id'];
	$obj->date = $this->_dbToDate($row['osc_date_envoi']);
	$obj->response = $row['osc_reponse'];
	$obj->rowclass = ($i++%2 == 0?'row1':'row2');
	$obj->libresponse = @$codes[$obj->response];
	$listeFrontal[] = $obj;
}

$smarty->assign('listeHistorique',$listeFrontal);

?>