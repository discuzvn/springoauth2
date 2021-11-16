<?php

/**
 *	  [Discuz! X] (C)2001-2099 Comsenz Inc.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  $Id: spacecp.inc.php 33645 2013-07-25 01:32:20Z nemohou $
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$action = dhtmlspecialchars($_GET['action']);
$setting = C::t('#springoauth2#spring_oauth_config')->first();

if ($action == 'callback') {

} elseif ($action == 'authorize') {
    $redirect_uri = $_G['siteurl'] . "plugin.php?id=springoauth2:authorize&action=callback";
    $url = $setting['issueruri'] . "/oauth2/authorize?response_type=code&client_id=" . $setting['clientid'] . "&scope=openid&redirect_uri=" . urlencode($redirect_uri);
    echo $url;
}
