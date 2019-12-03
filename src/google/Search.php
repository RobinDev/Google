<?php

namespace rOpenDev\Google;

abstract class Search
{
    use CacheTrait;
    use ConfSearchTrait;
    use SleepTrait;

    // =======
    // -------
    // =======

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
        $url = 'https://www.google.'.$this->tld.'/search?'.$this->generateParameters();

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
        /* Google respond :
         * We're sorry...... but your computer or network may be sending automated queries.
         * To protect our users, we can't process your request right now.'
         */
        if (false !== strpos($output, '<title>Sorry...</title>')) {
            return 2;
        }

        /* Captcha Google */
        elseif (false !== strpos($output, 'e=document.getElementById(\'captcha\');if(e){e.focus();}')) {
            return 1;
        }

        /* RAS */
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

            $extract = new ExtractResults($output);

            $this->result = array_merge($this->result, $extract->getOrganicResults());

            //h3 > a[href]
            if ($this->nbrPage > 1 && $extract->getNextPageLink()) {
                $url = 'https://www.google.'.$this->tld.str_replace('&amp;', '&', $extract->getNextPageLink());
            } else {
                break;
            }
        }

        return $this->result;
    }

    /**
     * getNbrResults va chercher le nombre de résulats que Google affiche proposer.
     *
     * @return int
     */
    public function getNbrResults()
    {
        $url = $this->generateGoogleSearchUrl();
        $output = $this->requestGoogle($url);
        if (false !== $output) {
            $html = new \simple_html_dom();
            $html->load($output);

            $rS = $html->find('#resultStats');
            if (isset($rS[0]->plaintext)) {
                $s = (string) $this->normalizeTextFromGoogle($rS[0]->plaintext);

                return intval(preg_replace('/[^0-9]/', '', $s));
            }
        }
    }
}
