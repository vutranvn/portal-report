<?php
require_once 'config.php';
require_once 'app.php';

$name = isset($_REQUEST['name'])?$_REQUEST['name']:'';
$time = isset($_REQUEST['time'])?$_REQUEST['time']:'';
$unit = isset($_REQUEST['unit'])?$_REQUEST['unit']:'';
$cus  = isset($_REQUEST['cus'])?$_REQUEST['cus'] : -1;

$from   = date("Y-m-d", strtotime("-90 days"));
if ( $from < '2016-09-01' ) {
    $from = '2016-09-01';
}
$to     = date('Y-m-d');

if( $time ) {
	$time_parse = explode(',', $time);
	if ( isset($time_parse[1]) ) {
		$from   = $time_parse[0];
		$to     = $time_parse[1];
	} else {
		$to = $time_parse[0];
		$from = date("Y-m-d", strtotime("$time_parse[0] -90 days"));
	}
}

$config = new Config_App();
if ( $name != '' && $name == 'traffbyisp' ) {

	$portals = $config->getPortal();
	$uLogin = $config->getUrlLogin();
	$urlApi = $config->getUrlApi();
    $topCus = $config->getTopCustomers();
    if ( $cus !== -1 ) {
        $ids = $topCus[$cus]['ids'];
    } else {
        $ids = array();
    }

	$result = trafficByIsp($portals, $uLogin, $urlApi, $cus, $ids, $from, $to);

	echo $result;
	die;
}

if ( $name == 'heatmap' ) {
	$urlSidebar = $config->getUrlSidebarMk();
	$user   = $config->getUserMk();
	$pass   = $config->getPassMk();

	$result = getHeatMK($urlSidebar[$cus], $user[$cus], $pass[$cus]);
	echo $result;
	die;
}

if ( $name == 'gettimerange' ) {
	$result = getRangeTime( $unit );
	echo $result;
	die;
}

if ( $name == 'timerangecus' ) {
	$result = getRangeTimeCus( $cus, $from, $to );
	echo $result;
	die;
}

function trafficByIsp( $portals, $login, $urlApi, $cus, $ids, $from, $to ) {
	$token  = getAccessToken( $portals, $login);
	$raw = array();
	foreach ($portals as $k => $portal) {
		$data = array(
			'isp=all',
			'type=body_bytes_sent',
			'service_type=all',
			"daterange=$from,$to",
		);
		if ( $cus !== -1 ) {
			$cus = $ids[$k];
		}

		$result = getDataChart('GET', $urlApi[$k], $cus, $token[$k], implode("&", $data));
		$raw[$k] = $result;
	}

	$fine   = array();
	foreach ($raw as $p => $pdata) {
		foreach ($pdata['data']['body_bytes_sent'] as $domain => $ddata) {
			foreach ($ddata as $key => $val) {
				$fine[$p][$key] += (isset($ddata[$key]) && $ddata[$key] > 0)?$ddata[$key]:0;
				$fine['date'][$key] = $pdata['time'][$key];
			}
		}
	}

	$final = array();
	$total = array();
	$final['date']  = $fine['date'];
	unset($fine['date']);
	foreach ($fine as $isp => $idata) {
		foreach ($idata as $k => $v) {
			$total[$k]       += (isset($idata[$k]) && $idata[$k] > 0)?$idata[$k]:0;
			$final[$isp][$k] = $v;
		}
	}

	$final['total'] = $total;
	$final = json_encode($final);

	return $final;
}

function getHeatMK( $url, $user, $pass ) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $user.":".$pass);
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 );

	$result = curl_exec($ch);
	$curl_errno = curl_errno($ch);
	if ($curl_errno > 0) {
		curl_close($ch);
		return 'timeout';
	}
	curl_close($ch);

	return $result;
}

function getRangeTime( $unit ) {

	$end  = strtotime('now');
	$view = '';
	if ( $unit == '1y' ) {
		$start = strtotime(date("Y-m-d H:i", strtotime("-1 years")));
		$view = 4;
	} elseif ( $unit == '1m' ) {
		$start = strtotime(date("Y-m-d H:i", strtotime("-30 days")));
		$view = 3;
	} elseif ( $unit == '1w' ) {
		$start  = strtotime(date("Y-m-d H:i", strtotime("-7 days")));
		$view = 2;
	} else {
		$start = strtotime(date("Y-m-d H:i", strtotime("-25 hours")));
		$view = 1;
	}

	$result = array(
		'start' => $start,
		'end'   => $end,
		'view'  => $view,
	);

	return json_encode($result);
}

function getRangeTimeCus( $customer, $from, $to ) {

	if( $customer == '' ) {
		$customer = 'vng';
	}

	$result = array(
		'from' => $from,
		'to'   => $to,
		'cus'  => $customer,
	);

	return json_encode($result);
}

function diffDays($dateFrom, $dateTo) {
	$dateTimeFrom   = strtotime($dateFrom);
	$dateTimeTo     = strtotime($dateTo);

	return ($dateTimeTo - $dateTimeFrom)/86400;
}