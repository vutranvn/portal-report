<?php
session_start();
require_once 'config.php';

function getAccessToken( $portals, $url ) {
	$token  = isset($_SESSION['token'])?$_SESSION['token']:'';
	$expire = isset($_SESSION['expire'])?$_SESSION['expire']:'';
	$config = new Config_App();
	$email  = $config->getEmail();
	$pass   = $config->getPassword();
	foreach ( $portals as $i => $portal ) {
		if (!isset($token[$i]) || !$token[$i] || !isset($expire[$i]) || strtotime("-5 minutes") > $expire[$i] ) {

			$postfields = array('email' => $email[$i], 'password' => $pass[$i]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url[$i]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

			$result = curl_exec($ch);
			$curl_errno = curl_errno($ch);
			if ($curl_errno > 0) {
				curl_close($ch);
				return 'timeout';
			}
			curl_close($ch);

			$result = json_decode($result, true);
			$expire[$i] = $result['data']['expire_at'];
			$token[$i]  = $result['data']['access_token'];

		}
	}

	$_SESSION['token']  = $token;
	$_SESSION['expire'] = $expire;

	return $token;
}

function getDataChart ($type, $api, $customer, $token, $data) {
	$ch = curl_init();
	$url = $api.$customer."?".$data;

	$header = array();
	$header[] = 'Content-length: 0';
	$header[] = 'Accept: application/json';
	$header[] = 'Authorization: Bearer '.$token;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 0);
	if ( $type == "POST" ) {
		curl_setopt($ch, CURLOPT_POST, 1);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // On dev server only!

	$result     = curl_exec($ch);
	$curl_errno = curl_errno($ch);
	if ($curl_errno > 0) {
		curl_close($ch);
		return 'timeout';
	}
	curl_close($ch);
	$result = json_decode($result, true);
	unset($result['domain']);
	unset($result['typeRequest']);
	unset($result['unit']);

	return $result;
}

function get_list_domain ($type, $customer, $token) {
	$crl = curl_init();

	$header = array();
	$header[] = 'Content-length: 0';
	$header[] = 'Accept: application/json';
	$header[] = 'Authorization: Bearer '.$token;

	curl_setopt($crl, CURLOPT_URL, "https://api.viettel-cdn.vn/v1/cdndomain/cus".$customer);
	curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
	 if ( $type == "POST" ) {
		 curl_setopt($crl, CURLOPT_POST, 1);
	 }
	curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!

	$result = curl_exec($crl);
	$curl_errno = curl_errno($crl);
	if ($curl_errno > 0) {
		curl_close($crl);
		return 'timeout';
	}
	curl_close($crl);

	return $result;
}

function encodeURI($url)
{
	$unescaped = array(
		'%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
		'%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
	);
	$reserved = array(
		'%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
		'%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
	);
	$score = array(
		'%23'=>'#'
	);
	return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));
}