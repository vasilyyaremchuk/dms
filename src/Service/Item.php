<?php

namespace App\Service;

use Twig\Environment;

class Item {
    /**
     * Render item
     */
    public function render(array $item, array $design, Environment $twigEnvironment) {
        // fix empty type
        if (!isset($item['type'])) {
            $item['type'] = 'hero';
        }
        $content = [];
        $background = 'bg-' . $design['global']['secondary_color'] . '-200';
        $content['decoration']['background'] = $background;

        $html = $twigEnvironment->render('components/items/' . $item['type'] . '.html.twig', [
            'content' => $content,
        ]);
        return $html;
    }
}
