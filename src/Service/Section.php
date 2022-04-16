<?php

namespace App\Service;

use Twig\Environment;

class Section extends Item{
    /**
     * Render section
     */
    public function render(array $section, array $decoration, Environment $twigEnvironment, array $context) {
        $content = [];

        // TBD: make smart Section select
        if (!isset($section['type'])) {
            if (count($section['items']) == 1) {
                $section['type'] = 'full-width';
            }
            else {
                $section['type'] = 'containered';
            }
        }

        foreach ($section['items'] as $key => $item) {
            $content['items'][] = parent::render($item, $decoration, $twigEnvironment, $section);
        }

        if ($section['color_mode'] == 'light') {
            $palete = $decoration['global']['light_palete'];
        }
        else {
            $palete = $decoration['global']['dark_palete'];
        }

        $background = 'bg-' . $decoration['global']['secondary_color'] . '-' . $palete;
        $content['decoration']['background'] = $background;

        $html = $twigEnvironment->render('components/sections/' . $section['type'] . '.html.twig', [
            'content' => $content,
        ]);
        return $html;
    }
}