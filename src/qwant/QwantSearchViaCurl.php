<?php

namespace rOpenDev\Qwant;

use rOpenDev\curl\CurlRequest;

class QwantSearchViaCurl extends QwantSearch
{
    protected $referrer;

    protected $cookie;

    /**
     * @return string|false Contenu html de la page
     */
    protected function requestGoogle(string $url)
    {
        $cache = $this->getCache($url);
        if ('' !== $cache) {
            return $cache;
        }

        $curl = new CurlRequest($url);
        $curl->setDefaultGetOptions()->setReturnHeader()->setEncodingGzip();

        if (isset($this->language)) {
            $curl->setOpt(\CURLOPT_HTTPHEADER, ['Accept-Language: '.$this->language]);
        }
        if (isset($this->userAgent)) {
            $curl->setUserAgent($this->userAgent);
        } else {
            if ($this->mobile) {
                $curl->setDestkopUserAgent();
            } else {
                $curl->setMobileUserAgent();
            }
        }

        if (isset($this->proxy)) {
            $curl->setProxy($this->proxy);
        }
        if (isset($this->referrer)) {
            $curl->setReferrer($this->referrer);
        }
        if (isset($this->cookie)) {
            $curl->setCookie($this->cookie);
        }

        $output = $curl->execute();

        /* Erreur lors de l'éxecution du cURL **/
        if ($curl->hasError()) {
            $this->cErrors = $curl->getErrors();
            $this->error = 1;

            return false;
        }

        $amIKicked = $this->amIKickedByGoogleThePowerful($output);
        if (false !== $amIKicked) {
            $this->error = $amIKicked;

            return false;
        }

        /* Tout est Ok, on enregistre et on renvoit le html **/
        $this->setCache($url, $output);

        $this->cookie = $curl->getCookies();
        $this->referrer = $curl->getEffectiveUrl();
        $this->execSleep();

        return $output;
    }
}
