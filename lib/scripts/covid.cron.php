<?php
// cron job for indexing covid tracker

$ch = curl_init("https://simpuslerep.com/api/ver1.0/Covid-Kab-Semarang/indexing-compiere.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html = curl_exec($ch);
curl_close($ch);

print_r($html);
