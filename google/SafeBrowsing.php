<?php

namespace rOpenDev\Google;

use rOpenDev\curl\CurlRequest;

class SafeBrowsing
{
    public $proxy;

    public static function get($url)
    {
        $current = new self();

        return $current->IsOkForsafeBrowsing($url);
    }

    /**
     * @return bool
     */
    public function IsOkForsafeBrowsing($url)
    {
        $url = 'https://transparencyreport.google.com/transparencyreport/api/v3/safebrowsing/status?site='.$url;
        $curl = new CurlRequest($url);
        $curl->setDestkopUserAgent();
        $curl->setReturnHeader();
        if (isset($this->proxy)) {
            $curl->setProxy($this->proxy);
        }
        $output = $curl->execute();
        $headers = $curl->getHeader();
        if ($curl->hasError() || false !== strpos($output, '<title>Sorry...</title>')) {
            return false;
        }

        //return strpos($output, 'Ce site n\'est actuellement pas') !== false ? true : false;
        return false !== strpos($headers[0], '200') ? true : false;
    }
}
