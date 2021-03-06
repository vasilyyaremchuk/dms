<?php

namespace App\Service;

class Decoration {
    /**
     * Load the content
     */
    public function load(string $industry='Default') {
        $string = file_get_contents('../content/' . $industry . '/decoration.json');
        $decoration = json_decode($string, true);

        // check and complete decoration settings TBD: make more universal
        $colors = $this->colorList();
        if (!isset($decoration['global']['primary_color']) || !$decoration['global']['primary_color']) {
            $decoration['global']['primary_color'] = $colors[array_rand($colors)];
        }
        if (!isset($decoration['global']['secondary_color']) || !$decoration['global']['secondary_color']) {
            $decoration['global']['secondary_color'] = $colors[array_rand($colors)];
        }
        if (!isset($decoration['global']['text_color']) || !$decoration['global']['text_color']) {
            $decoration['global']['text_color'] = $colors[array_rand($colors)];
        }
        $sizes = $this->sizeList();
        if (!isset($decoration['global']['text_size']) || !$decoration['global']['text_size']) {
            $decoration['global']['text_size'] = $sizes[array_rand($sizes)];
        }
        if (!isset($decoration['global']['headline_size']) || !$decoration['global']['headline_size']) {
            $decoration['global']['headline_size'] = $sizes[array_rand($sizes)];
        }
        if (!isset($decoration['global']['button_size']) || !$decoration['global']['button_size']) {
            $decoration['global']['button_size'] = $sizes[array_rand($sizes)];
        }
        $dark_palete = [100, 200, 300];
        $light_palete = [600, 700, 800];
        if (!isset($decoration['global']['light_palete']) || !$decoration['global']['light_palete']) {
            $decoration['global']['light_palete'] = $light_palete[array_rand($light_palete)];
        }
        if (!isset($decoration['global']['dark_palete']) || !$decoration['global']['dark_palete']) {
            $decoration['global']['dark_palete'] = $dark_palete[array_rand($dark_palete)];
        }
        return $decoration;
    }

    private function colorList() {
        return [
            'slate',
            'gray',
            'neutral',
            'stone',
            'red',
            'orange',
            'amber',
            'yellow',
            'lime',
            'green',
            'emerald',
            'teal',
            'cyan',
            'sky',
            'blue',
            'indigo',
            'violet',
            'purple',
            'fuchsia',
            'pink',
            'rose'
        ];
    }
    private function sizeList() {
        return [
            'small',
            'medium',
            'large'
        ];
    }

}
