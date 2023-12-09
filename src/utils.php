<?php

$formatCapacity = function (string $capacity): int {
    if (!empty($capacity)) {
        if (str_contains($capacity, "GB")) {
            $GBValue = explode('GB', $capacity, 2)[0];
            return intval($GBValue) * 1024;
        } elseif (str_contains($capacity, "MB")) {
            $MBValue = explode('MB', $capacity, 2)[0];
            return intval($MBValue);
        } else {
            return 0;
        }
    } else return 0;
};

$formatAvailability = function (string $text):string {
    if ($text && str_contains(strtolower($text), 'in stock')) {
        return "true";
    } else {
        return "false";
    }
};
