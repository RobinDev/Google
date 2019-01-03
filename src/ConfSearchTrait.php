<?php

namespace rOpenDev\Google;

trait ConfSearchTrait
{
    // =======
    // Conf var
    // =======

    /** @var string Contain the string query we will ask to Google Search * */
    protected $q;

    /** @var int Max number of result pages we want to extract * */
    protected $nbrPage = 1;

    /** @var array */
    protected $result = [];

    /** @var string Contain the Google TLD we want to query * */
    protected $tld = 'com';

    /** @var string Contain the language we want to send via HTTP Header Accept-Language (language[-local], eg. : en-US) * */
    protected $language = 'en-US';

    /** @var array Google Search URLs parameters (Eg. : hl => en) * */
    protected $parameters = [];

    /** @var string Contain http proxy settings * */
    protected $proxy;

    /** @var bool If the request need to emule a mobile * */
    protected $mobile = false;

    /** @var string Contain the user-agent we will send via HTTP Headers * */
    protected $userAgent;

    public function __construct(string $kw)
    {
        $this->q = $kw;
    }

    public function setMobile($bool)
    {
        $this->mobile = $bool;

        return $this;
    }

    public function setTld(string $tld)
    {
        $this->tld = $tld;

        return $this;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    public function setParameter($k, $v)
    {
        $this->parameters[$k] = $v;

        return $this;
    }

    public function setNbrPage(int $nbr)
    {
        $this->nbrPage = $nbr;

        return $this;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Si Aucun user-agent n'est précisé avant la requête, le script chargera l'user-agent par défault
     * de la class curlRequest (setDestkopUserAgent).
     */
    public function setUserAgent($ua)
    {
        $this->userAgent = $ua;

        return $this;
    }
}
