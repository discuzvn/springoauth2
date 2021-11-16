<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: install.php 8889 2010-04-23 07:48:22Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS pre_spring_oauth_config (
    `issueruri` char(155) NOT NULL default '',
    `clientid` char(255) NOT NULL default '',
    `clientsecret` char(255) NOT NULL default '',
    PRIMARY KEY (issueruri)
  ) TYPE=MyISAM;

REPLACE INTO pre_spring_oauth_config VALUES ('http://171.244.57.221:9000', 'grant-02', '123456');
EOF;

runquery($sql);

$finish = TRUE;

?>