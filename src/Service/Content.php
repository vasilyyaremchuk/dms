<?php

namespace App\Service;

class Content {
    /**
     * Load the content
     */
    public function load() {
        $string = file_get_contents("./index.json");
        $data = json_decode($string, true);
        return $data;
    }
}