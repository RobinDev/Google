<?php

namespace rOpenDev\Qwant;

use rOpenDev\Google\CacheTrait;
use rOpenDev\Google\ConfSearchTrait;
use rOpenDev\Google\SleepTrait;

abstract class QwantSearch
{
    use CacheTrait;
    use ConfSearchTrait;
    use SleepTrait;

    // =======
    // -------
    // =======

    protected $offset = 0;

    /** @var int Current page * */
    protected $page = 1;

    /**
     * @var string contain the current error
     */
    protected $error;

    protected $errors = [
        1 => 'Google Captcha',
        2 => 'Google `We\'re sorry` (flagged as automated request)',
        3 => 'Erreurs cURL',
    ];

    /**
     * Contient les erreurs provenant du cURL.
     */
    public $cErrors;

    public function generateGoogleSearchUrl()
    {
        $this->setParameter('q', $this->q);
        // ToSearchFromFranceInFrench, move it to config (todo)
        $defaultParameter = 'r=FR&sr=fr&l=fr_fr&h=0&s=1&a=1&b=1&vt=0&hc=1&smartNews=1&smartSocial=1&theme=0&i=1&donation=0&qoz=1&shb=1&shl=1';
        //$url = 'https://www.qwant.com/search?'.$defaultParameter.'&q='.urlencode($this->q);//.$this->generateParameters();
        $url = 'https://api.qwant.com/api/search/web?count=10&q='.urlencode($this->q).'&t=web&device=desktop&extensionDisabled=true&safesearch=1&locale=fr_FR&uiv=4';

        return $url;
    }

    protected function generateParameters()
    {
        return http_build_query($this->parameters, '', '&');
    }

    /**
     * @return string|false Contenu html de la page
     */
    abstract protected function requestGoogle(string $url);

    /*
     * Am I Kicked By Google ? Did you reach the google limits ?!
     *
     * @param string $output Html source
     *
     * @return int|false
     */
    protected function amIKickedByGoogleThePowerful($output)
    {
        return false;
    }

    /**
     * @return string explaining the error
     */
    public function getError()
    {
        if (null !== $this->error) {
            return $this->errors[$this->error];
        }

        return false;
    }

    /**
     * @return array|false containing the results with column type, link, title
     */
    public function extractResults()
    {
        for ($this->page = 1; $this->page <= $this->nbrPage; ++$this->page) {
            if (!isset($url)) {// On génère l'url pour la première requète... Ensuite, on utilisera le lien Suivant.
                $url = $this->generateGoogleSearchUrl();
            }

            $output = $this->requestGoogle($url);
            if (false === $output) {
                return false;
            }

            $extract = $this->extractResultsFromJson(json_decode($output, true));
            //var_dump($extract); exit;
            $this->numberItemsJustExtracted = count($extract);
            $this->result = array_merge($this->result, $extract);

            //h3 > a[href]
            $nextPageLink = $this->getNextPageLink();
            if ($this->nbrPage > 1 && $nextPageLink) {
                $url = $nextPageLink;
            } else {
                break;
            }
        }

        return $this->result;
    }

    public function extractResultsFromJson($json)
    {
        $results = [];

        if (isset($json['data']['result']['items'])) {
            foreach ($json['data']['result']['items'] as $item) {
                $results[] = [
                    'type' => 'organic',
                    'title' => strip_tags($item['title']),
                    'link' => $item['url'],
                ];
            }
        }

        return $results;
    }

    public function getNextPageLink()
    {
        if ($this->offset > 90 || $this->numberItemsJustExtracted < 10) {
            return false;
        }

        $this->offset = $this->offset + 10;

        return $this->generateGoogleSearchUrl().'&offset='.$this->offset;
    }
}
