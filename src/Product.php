<?php

namespace App;

require('utils.php');

class Product
{
    public string $title;
    public int $price;
    public string $imageUrl;
    public string $capacityMB;
    public string $colour;
    public string $availabilityText;
    public string $isAvailable;
    public string | null $shippingText;
    public string | null $shippingDate;

    public function __construct(string $title, string $price, string $imageUrl, string $capacity, string $colour, string $availability, string $isAvailable, string | null $delivery)

    {
        $this->title = $title;

        $this->price = $price;

        $this->imageUrl = $imageUrl;

        $this->capacityMB = $capacity;

        $this->colour = $colour;

        $availabilityTextArray = explode('Availability: ', $availability);
        if (count($availabilityTextArray) >= 2) {
            $this->availabilityText = $availabilityTextArray[1];
        }

        $this->isAvailable = $isAvailable;

        $this->shippingText = $delivery ? $delivery : null;

        $this->shippingDate = $delivery ? date(strtotime($delivery)) : null;
    }
}
