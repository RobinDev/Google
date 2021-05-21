<?php

namespace rOpenDev\Google;

use rOpenDev\curl\CurlRequest;

class SearchViaCurl extends Search
{
    protected $referrer;

    protected $cookie;

    /**
     * @return string|false Contenu html de la page
     */
    protected function requestGoogle(string $url, bool $redir = false)
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
        file_put_contents('debug.html', $output);

        /* Erreur logs de l'éxecution du cURL **/
        if ($curl->hasError()) {
            $this->cErrors = $curl->getErrors();
            $this->error = 3;

            return false;
        }

        $amIKicked = $this->amIKickedByGoogleThePowerful($output);
        if (false !== $amIKicked) {
            $this->error = $amIKicked;

            return false;
        }

        if ($redirection = $this->getRedirection($output)) {
            if (true === $redir) {
                $this->error = 4;

                return false;
            }

            return $this->requestGoogle('https://www.google.'.$this->tld.$redirection, true);
        }

        /* Tout est Ok, on enregistre et on renvoit le html **/
        $this->setCache($url, $output);

        $this->cookie = $curl->getCookies();
        $this->referrer = $curl->getEffectiveUrl();
        $this->execSleep();

        return $output;
    }

    /**
     * Retrieve a meta refresh.
     */
    private function getRedirection(string $html): string
    {
        if (preg_match('/content="\d+;url=(.*?)"/i', $html, $match)) {
            return str_replace('&amp;', '&', $match[1]);
        }

        return '';
    }
}
