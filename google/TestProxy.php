<?php

namespace rOpenDev\Google;

class TestProxy
{
    protected $types;

    public static function go($proxy)
    {
        $current = new self();

        return $current->isPorxyValid($proxy);
    }

    /**
     * Test si un proxy n'est pas déjà cramé par Google. Si aucun proxy n'est enregistré pour
     * la requête (via setProxy), la fonction test l'IP locale.
     *
     * @return bool
     */
    public function isProxyValid(?string $proxy = null)
    {
        $keywords = ['bateau', 'avion', 'navire', 'seconde', 'bac', 'piscine', 'fuser', 'place', 'homme', 'femme',  'quad', 'moto', 'velo',
                           'enfant', 'poilu', 'voiture', 'oiseau', 'singe', 'animaux', 'nature', 'paysage', 'jeux', 'maison', 'paysage', 'jardin', ];

        $GoogleSerp = new Search($keywords[array_rand($keywords)]);
        $GoogleSerp->setTld('fr')->setLanguage('fr')->setSleep(0)->setNbrPage(1)->setCache(false);
        if ($proxy) {
            $GoogleSerp->setProxy($proxy);
        }
        $r = $GoogleSerp->extractResults();

        return false === $r || empty($r) ? false : true;
    }
}
