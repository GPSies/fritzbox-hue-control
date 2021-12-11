<?php

// 2021-12-10 by K. Bechtold, Berlin

// sudo apt install php7.4-cli
// sudo apt-get install php-soap
// sudo apt-get install php-curl

// Crontab -e (create sh script with "php fritzbox-hue-control.php")
// */10 * * * * /home/klaus/fritzbox-hue-control.sh >/dev/null 2>&1

$hue_switch_on = '{"on":true}';
$hue_switch_off = '{"on":false}';
$hue_bridge_address = 'http://192.168.178.21/api/';
$hue_bridge_api_key = '123456789abc_please_replace_by_own_key';
$hue_steckdose_oben = $hue_bridge_address . $hue_bridge_api_key . '/lights/45/state';
$hue_steckdose_unten = $hue_bridge_address . $hue_bridge_api_key . '/lights/46/state';

$fritzbox_ip = 'fritz.box';
$tr64_port = '49000';

$lineEnd = php_sapi_name() === 'cli' ? PHP_EOL : '<br />';

$devices = ['Klauss-S21', 'Gesches-iPhone', 'Emmas-iPhone', 'Pauls-iPhone'];

echo('Request to: ' . $fritzbox_ip . '...' . $lineEnd);

$client = new SoapClient(
	null,
	array(
		'location' => 'http://'. $fritzbox_ip . ':' . $tr64_port . '/upnp/control/hosts',
		'uri' => 'urn:dslforum-org:service:Hosts:1',
		'soapaction' => 'urn:dslforum-org:service:Hosts:1#GetSpecificHostEntry',
		'noroot' => False
	)
);

$numberOfEntries = $client->GetHostNumberOfEntries();

echo('Number of devices: ' . $numberOfEntries . $lineEnd);

$devicesAtHome = [];
if (!(is_soap_fault($numberOfEntries))) {
  for ($i=0; $i<$numberOfEntries; $i++) {
    $entry = $client->GetGenericHostEntry(new SoapParam($i, 'NewIndex'));
		// echo('Device found: ' . $entry['NewHostName'] . $lineEnd);
		if (in_array($entry['NewHostName'], $devices) && ($entry['NewActive'] == 1)) {
		 $devicesAtHome[] = $entry['NewHostName'];
		 echo($entry['NewHostName'] . ' is at home' . $lineEnd);
		}
	}
}

if (empty($devicesAtHome)) {
	echo('No one there, now turn on the cam...' . $lineEnd);
	Helper::callRestAPI($hue_steckdose_oben, $hue_switch_on, $lineEnd);
	Helper::callRestAPI($hue_steckdose_unten, $hue_switch_on, $lineEnd);
}
else {
	echo('Someone there, turn off the cam now...' . $lineEnd);
	Helper::callRestAPI($hue_steckdose_oben, $hue_switch_off, $lineEnd);
	Helper::callRestAPI($hue_steckdose_unten, $hue_switch_off, $lineEnd);
}

class Helper {

	public static function callRestAPI($uri, $json, $lineEnd) {

		$headers = array (
	   	'Content-Type: application/json; charset=utf-8',
	    'Content-Length: ' . strlen($json)
	  );

	  $channel = curl_init($uri);
	  curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($channel, CURLOPT_CUSTOMREQUEST, 'PUT');
	  curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
	  curl_setopt($channel, CURLOPT_POSTFIELDS, $json);
	  curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 10);

	  $content = curl_exec($channel);
	  $statusCode = curl_getInfo($channel, CURLINFO_HTTP_CODE);

	  curl_close($channel);

		echo ($content . $lineEnd);

	  return $statusCode;

	}

}

?>
