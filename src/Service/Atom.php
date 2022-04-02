<?php

namespace App\Service;

use Twig\Environment;

class Atom {
    /**
     * Render atoms
     */
    public function render(array $atom, Environment $twigEnvironment) {
        // validate classes
        if (!isset($atom['classes'])) {
            $atom['classes'] = 'text-gray-700'; // logic TBD
        }
        // render only existing content stuff
        if (!isset($atom['content'])) {
            return '';
        }
        // fix empty type
        if (!isset($atom['type'])) {
            if (is_array($atom['content']) && isset($atom['content']['url'])) {
                $atom['type'] = 'link';
            }
            if (is_string($atom['content']) && strlen($atom['content']) > 128) {
                $atom['type'] = 'text';
            }
            if (is_string($atom['content']) && strlen($atom['content']) < 128) {
                $atom['type'] = 'headline';
            }
        }
        if (!isset($atom['type'])) {
            $atom['type'] = 'text';
            $atom['content'] = 'Error: invalid atom type!';
        }
        if ($atom['type'] == 'image' && !isset($atom['alt'])) {
            $atom['alt'] = 'Placeholder image'; // TBD: get it from context
        }
        $html = $twigEnvironment->render('components/atoms/' . $atom['type'] . '.html.twig', [
            'classes' => $atom['classes'],
            'content' => $atom['content'],
            'alt' => isset($atom['alt']) ? $atom['alt'] : '',
        ]);
        return $html;
    }
}
