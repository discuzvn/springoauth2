<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: cloud_connect.php 33756 2013-08-10 06:32:48Z nemohou $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$op = $_GET['op'];

$_GET['anchor'] = in_array($_GET['anchor'], array('setting', 'service')) ? $_GET['anchor'] : 'setting';
$current = array($_GET['anchor'] => 1);

if (!$_G['inajax']) {
	cpheader();
}

if ($_GET['anchor'] == 'setting') {
    $setting = C::t('#springoauth2#spring_oauth_config')->first();
    if(!submitcheck('connectsubmit')) {
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=springoauth2&pmod=admincp', 'connectsubmit');
        showtableheader();
        showsetting('Issuer URI', 'issuerurinew', $setting['issueruri'], 'text');
        showsetting('Client ID', 'clientidnew', $setting['clientid'], 'text');
        showsetting('Client secret', 'clientsecretnew', $setting['clientsecret'], 'text');
        showtagfooter('tbody');
        showsubmit('connectsubmit');
        showtablefooter();
        showformfooter();
    } else {
        C::t('#springoauth2#spring_oauth_config')->update_config($_GET['issuerurinew'], $_GET['clientidnew'], $_GET['clientsecretnew']);
        cpmsg('connect_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier=springoauth2&pmod=admincp', 'succeed');
    }
}
