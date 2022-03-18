<?php declare(strict_types=1);

namespace App\Http\Controllers\Productcrawler;

use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class AdidasProductCrawler extends \App\Helpers\Crawler
{
    protected array $modelNos = [];
    public function searchForProduct(int $modelNo): array
    {
        /** in dem Fall wird man zur richtigen Seite weitergeleitet, wenn man terrex-blue-blazes-t-shirt/ in die URL eingibt.
         * Die Modellnummer alleine anzuhängen endet in einem 404
         */
        $productURL = "https://www.adidas.de/terrex-blue-blazes-t-shirt/" . $modelNo . ".html";
        $html = $this->crawlURLWithSelenium($productURL);
        // gets redirected to correct page
        $crawler = new Crawler($html);

        /**
         * Hier bekommt man über die canonical die URL (einfach canonical googlen)
         * Kann mit der Chrome-Erweiterung META SEO Inspector nachvollzogen werden
         */
        $productURL = $crawler->filter('#meta-canonical')->attr('href');
        dump($productURL);
        $html = $this->crawlURLWithSelenium($productURL);
        $crawler = new Crawler($html);
        if (Str::contains($productURL,
            $modelNo)) {
            $productName = $crawler->evaluate('string(//h1)')[0];
            $productURL = $crawler->filter('#meta-canonical')->attr('href');
        }
        return [
            "productName" => $productName,
            "productURL" => $productURL
        ];
    }
}
