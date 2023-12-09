<?php

namespace App;

require 'vendor/autoload.php';
require 'utils.php';

class Scrape
{
    private array $products = [];
    public $base_url = "https://www.magpiehq.com/developer-challenge/";

    public function run(): void
    {
        $document = ScrapeHelper::fetchDocument($this->base_url . "smartphones/");

        $productIndex = 0;

        // Set Page Info
        $pageInfoString = $document->filter('#products p.block.text-center')->text();

        preg_match_all('!\d+!', $pageInfoString, $matches);

        $first_page = 1;
        $last_page = null;

        if ($matches) {
            $last_page = $matches[0][count($matches[0]) - 1];
        }

        $pages = range($first_page, $last_page ? $last_page : $first_page);


        foreach ($pages as $page) {

            echo "Currently scraping page $page...", PHP_EOL;

            $document->filter('.product > div')->each(function ($product_node) use (&$productIndex) {

                $product_node->filter('div')->eq(0)->filter('span.rounded-full')->each(function ($variant) use (&$productIndex, &$product_node) {

                    global $formatCapacity;
                    global $formatAvailability;

                    $title = $product_node->filter('span.product-name')->text();
                    $price = substr($product_node->filter('div.my-8')->text(), 2);
                    $imageUrl = $this->base_url . substr($product_node->filter('img')->attr('src'), 3);
                    $capacity = $formatCapacity($product_node->filter('span.product-capacity')->text());
                    $colour = $variant->attr('data-colour');

                    // Availability
                    $availabilityText = null;
                    $availabilityTextArray = explode('Availability: ', $product_node->filter('div.my-4.text-sm.text-center')->first()->text());
                    if (count($availabilityTextArray) >= 2) {
                        $availabilityText = $availabilityTextArray[1];
                    }
                    $product['availabilityText'] = $availabilityText;
                    //

                    // Shipping
                    $deliveryNodes = $product_node->filter('div.my-4.text-sm.text-center');
                    $shippingText = count($deliveryNodes) > 1 ? $deliveryNodes->eq(1)->text() : "";
                    //

                    $product = new Product($title, $price, $imageUrl, $capacity, $colour, $availabilityText, $formatAvailability($availabilityText), $shippingText);

                    $this->products[] = $product;

                    $productIndex++;
                });
                return;
            });

            echo "Page $page scrape complete.", PHP_EOL, PHP_EOL;

            $document = ScrapeHelper::fetchDocument("$this->base_url/smartphones/?page=" . $page + 1);
        }

        echo "All available pages have been scraped.", PHP_EOL, PHP_EOL;

        file_put_contents('output.json', json_encode(array_unique(array($this->products))));
    }
}

$scrape = new Scrape();
$scrape->run();

// Alternative way to get availability text

                // $availabilityTextArray = explode('Availability: ', $product_node->filter('div.my-4.text-sm.text-center')->reduce(function ($node) {
                //     return str_starts_with($node->text(), 'Availability');
                // })->text());
