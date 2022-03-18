<?php declare(strict_types=1);

namespace App\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client;

abstract class Crawler
{
    protected array $modelNos;
    protected Client $client;
    protected RemoteWebDriver $seleniumDriver;
    protected \Symfony\Component\DomCrawler\Crawler $crawler;

    public function __construct()
    {
        $this->client = $this->initClient();
        foreach ($this->modelNos as $modelNo) {
            $productData = $this->searchForProduct($modelNo);

        }
    }

    public function initClient()
    {
        $client = new Client();
        $client->setClient(new GuzzleClient(array(
            // DISABLE SSL CERTIFICATE CHECK
            'verify' => false,
            'curl' => array(
                CURLOPT_TIMEOUT => 60,
            ),
        )));
        return $client;
    }

    protected function crawlURLWithSelenium($url): ?string
    {
        if (!isset($this->seleniumDriver)) {
            $host = 'http://selenium:4444/wd/hub'; // this is the default
            $capabilities = DesiredCapabilities::chrome();
            $options = new ChromeOptions();
            $options->addArguments(['--blink-settings=imagesEnabled=false']);
            $capabilities->setCapability(ChromeOptions::CAPABILITY,
                $options);
            $this->seleniumDriver = RemoteWebDriver::create($host,
                $capabilities,
                15000,
                60000);
        }
        try {
            $this->seleniumDriver->get($url);
            $this->seleniumDriver->wait(3);
            $html = $this->seleniumDriver->getPageSource();
        } catch (WebDriverCurlException $e) {
            dump($url . $e->getMessage());
            return null;
        } catch (UnknownServerException $e) {
            dump($url . $e->getMessage());
            return null;
        }
        return $html;
    }

    protected function crawlURL($url):?string
    {
        try {
            $client = new Client();
            $client->setHeader('User-Agent',
                "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
            //Avoid hanging in endless redirect loops
            $client->setMaxRedirects(10);
            $crawler = $client->request('GET',
                $url,
                []);
            $html = $crawler->html();
        } catch (\ErrorException $e) {
            dump("Es ist ein Fehler aufgetreten!");
        }
        return $html;
    }

    protected abstract function searchForProduct(int $modelNo): array
    {

    }
}