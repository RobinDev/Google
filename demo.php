<?php


include 'vendor/autoload.php';

/**/
use rOpenDev\Google\SearchViaCurl;
use rOpenDev\Google\TestProxy;
use rOpenDev\Google\SafeBrowsing;

$Google = new SearchViaCurl('qwanturank');

$Google->setTld('fr')
         ->setLanguage('fr')
         ->setSleep(6)  // to wait between 2 requests on Google
         ->setCacheFolder(null) // to disable storing in the /tmp folder
         //->setCacheExpireTime(86400) // 1 Day
         ->setNbrPage(1) // Nbr de page à extraire
         ->setParameter('num', 100) // to add a parameter in the search url
;

var_dump($Google->extractResults());

/**/

use rOpenDev\Qwant\QwantSearchViaCurl;

$Qwant = new QwantSearchViaCurl('qwanturank');

$Qwant->setLanguage('fr')
         ->setSleep(6)  // to wait between 2 requests on Qwant
         ->setCacheFolder('./tmp') // to disable storing in the /tmp folder
         ->setNbrPage(10) // Nbr de page à extraire
;

$results = $Qwant->extractResults();

var_dump($results);
