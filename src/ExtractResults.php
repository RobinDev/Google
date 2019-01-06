<?php

namespace rOpenDev\Google;

class ExtractResults
{
    /**
     * @var array from \rOpenDev\Google\ResultTypes.php
     */
    protected $types;
    /**
     * @var object dom
     */
    protected $html;

    public function __construct(string $source)
    {
        $this->html = new \simple_html_dom();
        $this->html->load($source);
    }

    public function getOrganicResults()
    {
        $results = $this->html->find(str_replace(';', ', ', $this->getSelector('organic')));
        $result = [];
        foreach ($results as $r) {
            $title = $r->find('h3, [role=heading]', 0);
            if ($title) {
                $result[] = [
                    'type' => 'organic',
                    'title' => $this->normalizeTextFromGoogle($title->innertext),
                    'link' => $this->getUrlFromGoogleSerpFromat($r->find('a', 0)->href),
                ];
            }
        }

        return $result;
    }

    /**
     * @return string or NULL if next page link not found
     */
    public function getNextPageLink()
    {
        if (isset($this->html->find('#pnnext, h3 > a[href]')[0])) {
            return $this->html->find('#pnnext')[0]->href;
        }
    }

    protected function getSelector(string $type)
    {
        if (null === $this->types) {
            $this->types = ResultsTypes::get();
        }

        return $this->types[$type];
    }

    protected static function getUrlFromGoogleSerpFromat($str)
    {
        preg_match('/\/url\?.*(q|url)=(http.+)&/SiU', $str, $m1);
        $str = isset($m1[2]) ? $m1[2] : $str;

        return $str;
    }

    protected function normalizeTextFromGoogle($text)
    {
        return htmlspecialchars_decode(html_entity_decode(strip_tags($text)));
    }
}
