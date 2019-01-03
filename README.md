# Google Helpers

Few PHP classes to manage request on Google Web Search & co.

```php
use rOpenDev\Google\SearchViaCurl;
use rOpenDev\Google\TestProxy;
use rOpenDev\Google\SafeBrowsing;

$Google = new SearchViaCurl('my kw');

$Google->setProxy('monproxie:monport:username:password')
         ->setTld('com')
         ->setLanguage('en')
         ->setSleep(6)  // to wait between 2 requests on Google
         ->setCacheFolder(null) // to disable storing in the /tmp folder
         //->setCacheExpireTime(86400) // 1 Day
         ->setNbrPage(10) // Nbr de page à extraire
         ->setParameter('num', 100) // to add a parameter in the search url
;

/**
 * @return array of array containing type, title, link values
 */
$Google->extractResults();

// Delete cache files
$Google->deleteCacheFiles();


/**
 * @return int
 */
$Google->getNbrResults()

/**
 * @return string explaining the error
 */
$Google->getError(); // $Google->cErrors contains curl errors

/* return an array */

TestProxy::go('monrpoxu'); // @return bool

SafeBrowsing::get('https://piedweb.com');// @return bool

```
## Contribute

Check coding standards before to commit : `php-cs-fixer fix src --rules=@Symfony --verbose && php-cs-fixer fix src --rules='{"array_syntax": {"syntax": "short"}}' --verbose`


### Contributors

* [Pied Web](https://piedweb.com)
* ...

## License

MIT (see the LICENSE file for details)


[![Latest Version](https://img.shields.io/github/tag/RobinDev/Google.svg?style=flat&label=release)](https://github.com/RobinDev/Google/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/RobinDev/Google/LICENSE.md)
[![Build Status](https://img.shields.io/travis/RobinDev/Google/master.svg?style=flat)](https://travis-ci.org/RobinDev/Google)
[![Quality Score](https://img.shields.io/scrutinizer/g/RobinDev/Google.svg?style=flat)](https://scrutinizer-ci.com/g/RobinDev/Google)
[![Total Downloads](https://img.shields.io/packagist/dt/ropendev/google.svg?style=flat)](https://packagist.org/packages/ropendev/google)
