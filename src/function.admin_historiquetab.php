<?php

if (!isset($gCms)) exit;


// Verification de la permission
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
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