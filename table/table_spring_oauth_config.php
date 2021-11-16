<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_connect_guest.php 29265 2012-03-31 06:03:26Z yexinhao $
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_spring_oauth_config extends discuz_table
{

	public function __construct()
	{
		$this->_table = 'spring_oauth_config';
		$this->_pk = 'issueruri';

		parent::__construct();
	}

	public function first()
	{
		$data = DB::fetch_all('SELECT * FROM ' . DB::table($this->_table));
		return $data[0];
	}

	public function update_config($issueruri, $clientid, $clientsecret)
	{
		return DB::query("UPDATE %t SET issueruri = %s, clientid = %s, clientsecret = %s", array($this->_table, $issueruri, $clientid, $clientsecret), false, true);
	}
}
