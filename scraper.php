<?php

// include $_SERVER['DOCUMENT_ROOT'] . '/scraper/simplehtmldom/simple_html_dom.php';

require_once(__DIR__ . '/vendor/autoload.php');

use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Scraper{

    // important urls
    public $log_in_url = 'https://ahrefs.com/user/login';
    public $dashboard_url = 'https://ahrefs.com/dashboard/metrics';

    // webdriver
    public $host = 'http://127.0.0.1:9515';
    public $user_firefox = false;
    // credentials
    public $email;
    public $password;

    public function __construct($email, $password){
        $this->email = $email;
        $this->password = $password;
    }

    public function login(){
        if ($this->user_firefox)
        {
            $this->driver = RemoteWebDriver::create(
                $this->host, 
                DesiredCapabilities::firefox()
            );
        }
        else
        {
            $this->driver = RemoteWebDriver::create(
                $this->host, 
                DesiredCapabilities::chrome()
            );
        }

        // go to log in page
        $this->driver->get($this->log_in_url);

        // fill in the email
        $this->driver->findElement(WebDriverBy::id('email_input'))->sendKeys($this->email);
        sleep(1);
        // fill in password
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys($this->password);
        sleep(1);
        // submit from password field
        $this->driver->findElement(WebDriverBy::name('password'))->submit();
        sleep(2);
    }

    public function getDomainRank($term){
        $this->driver->get($this->dashboard_url);
        // enter search term in the search field
        $this->driver->findElement(WebDriverBy::id('dashboard_target'))->sendKeys($term);

        // submit search
        $this->driver->findElement(WebDriverBy::id('dashboard_target'))->submit();

        sleep(5);

        // return domain rank
        return [
            'domain_search' => $term,
            'domain_rank' => $this->driver->findElement(WebDriverBy::cssSelector('#DomainRatingContainer > span'))->getText()
        ];
    }
}

$scraper = new Scraper('leanrankio@gmail.com', 'rankmeplease');
$scraper->login();

$domains = [
    'betfy.co.uk',
    'skilled.co'
];

foreach($domains as $domain){
    $search_obj = $scraper->getDomainRank($domain);
    echo $domain . " / Rank: " . $search_obj['domain_rank'] . '<br />';
}

?>