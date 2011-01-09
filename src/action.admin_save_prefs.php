<?php

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