<?php

namespace App\Service;

class Content {
    /**
     * Load the content
     */
    public function load(string $industry='Default') {
        $string = file_get_contents('../content/' . $industry . '/content.json');
        $data = json_decode($string, true);
        return $data;
    }
}
