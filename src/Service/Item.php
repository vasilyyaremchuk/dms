<?php

namespace App\Service;

use Symfony\Component\ErrorHandler\Debug;
use Twig\Environment;

class Item extends Atom {
    /**
     * Render item
     */
    public function render(array $item, array $decoration, Environment $twigEnvironment, array $context) {
        $content = [];
        // select item type
        $has_headline = false;
        $has_text = false;
        $has_image = false;
        $has_button = false;
        // TBD: put it in section settings
        if (!isset($item['color_mode']) || !$item['color_mode']) {
            $color_mode = ['light', 'dark'];
            $item['color_mode'] = $color_mode[array_rand($color_mode)];
        }
        foreach ($item['atoms'] as $key => $atom) {
            $item['atoms'][$key] = parent::atom_type_validate($atom);
            // TBD: more universal algorithm
            if ($item['atoms'][$key]['type'] == 'headline') {
                $has_headline = true;
                $content['headline'] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $item);
            }
            if ($item['atoms'][$key]['type'] == 'text') {
                $has_text = true;
                $content['text'] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $item);
            }
            if ($item['atoms'][$key]['type'] == 'image') {
                $has_image = true;
                $content['image'] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $item);
            }
            if ($item['atoms'][$key]['type'] == 'button') {
                $has_button = true;
                $content['button'] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $item);
            }
        }
        if ($has_headline && $has_text && $has_image && $has_button) {
            $item['type'] = 'hero';
            $variant = ['flex-row', 'flex-row-reverse'];
            $content['variant'] = $variant[array_rand($variant)];
        }
        if ($has_headline && $has_text && !$has_image && $has_button) {
            $item['type'] = 'cta';
        }

        if ($item['color_mode'] == 'light') {
            $palete = $decoration['global']['light_palete'];
        }
        else {
            $palete = $decoration['global']['dark_palete'];
        }

        $background = 'bg-' . $decoration['global']['secondary_color'] . '-' . $palete;
        $content['decoration']['background'] = $background;

        $html = $twigEnvironment->render('components/items/' . $item['type'] . '.html.twig', [
            'content' => $content,
        ]);
        return $html;
    }
}
