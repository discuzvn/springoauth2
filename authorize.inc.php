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


if($_G['uid']) {
	showmessage('Bạn đã đăng nhập', NULL, array());
}

require_once libfile('function/member');

$action = dhtmlspecialchars($_GET['action']);
$setting = C::t('#springoauth2#spring_oauth_config')->first();

$redirect_uri = $_G['siteurl'] . "grant-01-oidc.php";

if ($action == 'callback') {
    $setting = C::t('#springoauth2#spring_oauth_config')->first();
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $setting['issueruri'] . '/oauth2/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'code='.$_GET['code'].'&grant_type=authorization_code&client_id='.$setting['clientid'].'&client_secret='.$setting['clientsecret'].'&redirect_uri=' . urlencode($redirect_uri),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $json = json_decode($response, true);
    if (isset($json['id_token'])) {
        $user = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $json['id_token'])[1]))), true);
        $username = $user['sub'];
        $fullName = $user['fullName'];
        $userId = $user['userId'];}

        loaducenter();
        $u = uc_get_user(addslashes($username));
        if($u && C::t('common_member')->fetch_uid_by_username($username)) {
            $uid = $u[0];
            $member = getuserbyuid($uid, 1);
            setloginstatus($member, $_GET['cookietime'] ? 2592000 : 0);
            C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
            $loginmessage = 'login_succeed';
            $location = 'forum.php';
            $param = array(
                'username' => $username,
                'uid' => $uid,
                'usergroup' => 'thành viên'
            );

            $extra = array(
                'showdialog' => true,
                'locationtime' => true,
                'extrajs' => $ucsynlogin
            );

            showmessage($loginmessage, $location, $param, $extra);
        } else {
            $password = md5(random(10));
            $uid = uc_user_register(addslashes($username), $password, $username, '', '', $_G['clientip']);
            if($uid <= 0) {
                if($uid == -1) {
                    showmessage('profile_username_illegal');
                } elseif($uid == -2) {
                    showmessage('profile_username_protect');
                } elseif($uid == -3) {
                    showmessage('profile_username_duplicate');
                } elseif($uid == -4) {
                    showmessage('profile_email_illegal');
                } elseif($uid == -5) {
                    showmessage('profile_email_domain_illegal');
                } elseif($uid == -6) {
                    showmessage('profile_email_duplicate');
                } else {
                    showmessage('undefined_action');
                }
            }
            $setting = C::t('common_setting')->fetch_all(array('initcredits', 'newusergroupid', 'bbname'));

            $init_arr = array('credits' => explode(',', $setting['initcredits']), 'profile'=>array(), 'emailstatus' => 1);

			C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $setting['newusergroupid'], $init_arr);
            updatecreditbyaction('realemail', $uid);

            require_once libfile('cache/userstats', 'function');
			build_cache_userstats();
            setloginstatus(array(
				'uid' => $uid,
				'username' => $_G['username'],
				'password' => $password,
				'groupid' => $setting['newusergroupid'],
			), 0);
			include_once libfile('function/stat');
			updatestat('register');
            $message = 'register_succeed';
            $locationmessage = 'register_succeed_location';
            $extra = array(
				'showid' => 'succeedmessage',
				'extrajs' => '<script type="text/javascript">'.
					'setTimeout("window.location.href =\''.$href.'\';", '.$refreshtime.');'.
					'$(\'succeedmessage_href\').href = \''.$href.'\';'.
					'$(\'main_message\').style.display = \'none\';'.
					'$(\'main_succeed\').style.display = \'\';'.
					'$(\'succeedlocation\').innerHTML = \''.lang('message', $locationmessage).'\';'.
				'</script>',
				'striptags' => false,
			);

            $param = array('bbname' => $setting['bbname'], 'username' => $_G['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']);
            showmessage($message, $url_forward, $param, $extra);
    }
} elseif ($action == 'authorize') {
    $url = $setting['issueruri'] . "/oauth2/authorize?response_type=code&client_id=" . $setting['clientid'] . "&scope=openid&redirect_uri=" . urlencode($redirect_uri);
    header('Location: ' . $url, true, 301);
}
