<?php

////////READ FILE///////////
$count=1;
$file = fopen("domains.txt","r");

while(! feof($file))
  {
$site=fgets($file);

$site =  preg_replace("/\r|\n/", "", $site);
$site = str_replace(' ', '', $site);

////////ADD TARGET///////////
$url = 'https://192.168.xx.xx:3443/api/v1/targets';
$data = array("address" => $site,"description" => "","type"=>"default","criticality" => 10);

echo "Domain: ".$site."\n";
echo "Number: ".$count."\n";

//echo bin2hex($site);

$postdata = json_encode($data);
//echo $postdata;
addretry:
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json','X-Auth:xxxxxxxAuthKeyHerexxxxxxxxxxxxxxx'));
$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
//print_r ($result);
if ($httpcode !=201)
{
echo "Target Adding Failed! Error ".$httpcode."\n";
goto addretry;
exit(0);
}
else  {
echo "Added Target!\n";
}
$json = json_decode($result, true);

//print_r($json);

//////////GET TARGET ID///////

$id = $json['target_id'];
 //   print_r ($id);
//echo 'HTTP code: ' . $httpcode;
///////SCHEDULE SCAN////////

$url = 'https://192.168.xx.xx:3443/api/v1/scans';
$data = array("target_id" => $id,"profile_id" => "11111111-1111-1111-1111-111111111111","schedule" => array("disable" => "true"));

$postdata = json_encode($data);
scanretry:
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json','X-Auth:xxxxxxxAuthKeyHerexxxxxxxxxxxxxxx'));
$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !=201)
{
echo "Scan Scheduling Failed! Error: ".$httpcode."\nExiting!\n";
goto scanretry;
exit(0);
}
else {
echo "Scan Scheduled!\n\n";
$count = $count+1;
}
//print_r ($result);

//echo 'HTTP code: ' . $httpcode;

}

fclose($file);
?>
