<?php
require __DIR__ . '/vendor/autoload.php';

$apiKey = getenv('API_KEY');
$tld = getenv('TLD');
$subdomain = getenv('SUBDOMAIN');

if (!$apiKey || !$subdomain || !$tld) {
    echo "API_KEY and TLD and SUBDOMAIN env vars are required\n";
    exit(1);
}

$updateFreqency = getenv('UPDATE_FREQUENCY') ?: 60;
$adapter = new \DigitalOceanV2\Adapter\BuzzAdapter($apiKey);
$doClient = new \DigitalOceanV2\DigitalOceanV2($adapter);

while (true) {
    $currentIp = json_decode(file_get_contents("http://ifconfig.me/all.json"), true)["ip_addr"];
    if (!$currentIp) {
        echo "Unable to get current IP from ifconfig.me\n";
        exit(1);
    }

    echo "Found current public ip: $currentIp\n";

    $existingDomainRecord = false;
    $domainRecords = $doClient->domainRecord()->getAll($tld);
    foreach ($domainRecords as $record) {
        if ($record->name == $subdomain && $record->type == "A") {
            echo "Subdomain record $subdomain exists and points to $record->data\n";
            $existingDomainRecord = $record;
        }
    }

    if ($existingDomainRecord) {
        if ($existingDomainRecord->data != $currentIp) {
            $doClient->domainRecord()->update($tld, $existingDomainRecord->id, $subdomain, $currentIp);
            echo "Updated subomdain record $subdomain.$tld with value $currentIp\n";
        }
    } else {
        $doClient->domainRecord()->create($tld, "A", $subdomain, $currentIp);
        echo "Created subomdain record $subdomain.$tld with value $currentIp\n";
    }

    echo "Next update in $updateFreqency minutes";
    sleep($updateFreqency * 60);
}
