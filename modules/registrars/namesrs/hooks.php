<?php

use WHMCS\Database\Capsule as Capsule;
use WHMCS\Domains\Domain as DomPuny;

add_hook("AfterRegistrarRegistration",1,function($vars)
{
  logModuleCall(
    'nameSRS',
    "Calling hook DOMAIN_STATUS_REGISTER",
    $vars['params']['sld'].'.'.$vars['params']['tld'],
    $vars
  );
  // set domain to PENDING status - until the callback/webhook updates it accordingly
  if($vars['params']['registrar']=='namesrs')//Only launch hook in this module
  {
    localAPI('UpdateClientDomain',Array(
      'domainid' => $vars['params']['domainid'],
      'status' => 'Pending Registration'
    ));
  }
});

add_hook("AfterRegistrarTransfer",1,function($vars)
{
  logModuleCall(
    'nameSRS',
    "Calling hook DOMAIN_STATUS_REGISTER",
    $vars['params']['sld'].'.'.$vars['params']['tld'],
    $vars
  );
  // set domain to PENDING status - until the callback/webhook updates it accordingly
  if($vars['params']['registrar']=='namesrs')
  {
    localAPI('UpdateClientDomain',Array(
      'domainid' => $vars['params']['domainid'],
      'status' => 'Pending Transfer'
    ));
  }
});

use WHMCS\View\Menu\Item as MenuItem;

// disable some of the built-in WHMCS menus in the sidebar as we either do not support
// the functionality or offer better implementations

add_hook('ClientAreaPrimarySidebar', 1, function (MenuItem $primarySidebar)
{
  $sidebar = $primarySidebar->getChild('Domain Details Management');
  if($sidebar)
  {
    //$sidebar->removeChild('Domain Contacts');
    //$sidebar->removeChild('Manage Private Nameservers');
    //$sidebar->removeChild('Manage Email Forwarding');
    //Remove this link in all modules.
  }
});


//We cancel send email registration confirmation
add_hook("EmailPreSend",1,function($vars)
{

  if($vars['mergefields']['domain_registrar']=='namesrs'&&$vars['mergefields']['domain_status']=='Pending Registration')
  {
    if ($vars['messagename']=='Domain Registration Confirmation') {$merge_fields = [];$merge_fields['abortsend'] = true; return $merge_fields;}
  }
});
//We cancel send email registration confirmation