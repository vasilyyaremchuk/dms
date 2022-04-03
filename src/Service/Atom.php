<?php

namespace App\Service;

use Twig\Environment;

class Atom {
    /**
     * Render atoms
     */
    public function render(array $atom, array $decoration, Environment $twigEnvironment) {
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
                $types = ['link', 'button'];
                $atom['type'] = $types[array_rand($types)];
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

        // TBD: more universal rules for classes
        if ($atom['type'] == 'link' || $atom['type'] == 'text') {
            $color = $atom['type'] == 'text' ? $decoration['global']['text_color'] : $decoration['global']['primary_color'];
            $atom['classes'] = 'text-'. $color . '-800';
            switch ($decoration['global']['text_size']) {
                case 'small':
                    $atom['classes'] .= ' text-base';
                    break;
                case 'large':
                    $atom['classes'] .= ' text-xl';
                    break;
                case 'meddium':
                default:
                    $atom['classes'] .= ' text-lg';
                    break;
            }
        }
        if ($atom['type'] == 'headline') {
            $color = $decoration['global']['primary_color'];
            $atom['classes'] = 'text-'. $color . '-800 font-bold';
            switch ($decoration['global']['text_size']) {
                case 'small':
                    $atom['classes'] .= ' text-3xl';
                    break;
                case 'large':
                    $atom['classes'] .= ' text-5xl';
                    break;
                case 'meddium':
                default:
                    $atom['classes'] .= ' text-4xl';
                    break;
            }
        }
        if ($atom['type'] == 'button') {
            $atom['classes'] = 'bg-' . $decoration['global']['primary_color'] . '-700 text-white font-bold';
            $atom['classes'] .= ($decoration['global']['button_type'] == 'circle') ? ' rounded-full' : ' rounded-md';
            switch ($decoration['global']['text_size']) {
                case 'small':
                    $atom['classes'] .= ' text-base';
                    break;
                case 'large':
                    $atom['classes'] .= ' text-xl';
                    break;
                case 'meddium':
                default:
                    $atom['classes'] .= ' text-lg';
                    break;
            }
        }

        $html = $twigEnvironment->render('components/atoms/' . $atom['type'] . '.html.twig', [
            'classes' => $atom['classes'],
            'content' => $atom['content'],
            'alt' => isset($atom['alt']) ? $atom['alt'] : '',
        ]);
        return $html;
    }
}
