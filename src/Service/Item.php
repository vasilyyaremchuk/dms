<?php

namespace App\Service;

use Symfony\Component\ErrorHandler\Debug;
use Twig\Environment;

class Item extends Atom {
    /**
     * Render item
     */
    public function render(array $item, array $decoration, Environment $twigEnvironment, array $context) {
        // get available item components, TBD: load it ones
        $number_siblings = count($context["items"]);
        $available_components = [];
        $path    = '../templates/components/items/';
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            $parts = explode('.', $file);
            if ($parts[1] == 'json') {
                $available_components[$parts[0]] = json_decode(file_get_contents($path . $file), true);
            }
        }
        // TBD: better validation of number of elements in column
        foreach ($available_components as $key => $available_component) {
            $available_components[$key]['available_elements'] = [];
            if (is_array($available_component['elements']) && !empty($available_component['elements'])) {
                foreach ($available_component['elements'] as $el_key => $element) {
                    for ($i = 0; $i< $element; $i++) {
                        $available_components[$key]['available_elements'][] = $el_key;
                    }
                }
            }
            if ($available_components[$key]["max_in_row"] >= $number_siblings && $available_components[$key]["min_in_row"] <= $number_siblings) {
                $available_components[$key]['score'] = 1;
            }
            else {
                $available_components[$key]['score'] = -100; // TBD: better way to deny
            }
            // force horizontal menu on the top
            if ($context['order'] == 1 && $key == 'horizontal-menu') {
                $available_components[$key]['score'] += 1;
            }

        }
        $content = [];
        // select item type
        $existing_components = [];
        foreach ($item['atoms'] as $key => $atom) {
            $item['atoms'][$key] = parent::atom_type_validate($atom);
            $existing_components[] = $item['atoms'][$key]['type'];
            // render atoms
            if (isset($content[$item['atoms'][$key]['type']])) {
                if (is_array($content[$item['atoms'][$key]['type']])) {
                    $content[$item['atoms'][$key]['type']][] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                }
                else {
                    $temp = $content[$item['atoms'][$key]['type']];
                    unset($content[$item['atoms'][$key]['type']]);
                    $content[$item['atoms'][$key]['type']] = [];
                    $content[$item['atoms'][$key]['type']][] = $temp;
                    $content[$item['atoms'][$key]['type']][] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                }

            }
            else {
                $content[$item['atoms'][$key]['type']] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
            }
        }
        // scoring to check the type
        foreach ($available_components as $av_key => $available_component) {
            if (isset($available_component['available_elements']) && is_array($available_component['available_elements'])) {
                // + itersection of elements
                $available_components[$av_key]['score'] += count(array_intersect($existing_components, $available_component['available_elements']));
                // - score if we have different number of elements
                $available_components[$av_key]['score'] -= abs(count($existing_components) - count($available_component['available_elements']));
            }
        }
        $item_type = '';
        $item_type_score = 0;
        foreach ($available_components as $key => $available_component) {
            if (is_array($available_component) && $available_component['score'] > $item_type_score) {
                $item_type_score = $available_component['score'];
                $item_type = $key;
            }
        }

        // TBD: validate if item type is set

        // randimise item type
        $item_types = [];
        foreach ($available_components as $key => $available_component) {
            if (is_array($available_component) && $available_component['score'] == $item_type_score) {
                $item_types[] = $key;
            }
        }
        $item_type = $item_types[array_rand($item_types)];
        if (!isset($item['type'])) {
            $item['type'] = $item_type;
        }
        else {
            $item_type = $item['type'];
        }

        // determen variant
        if (!empty($available_components[$item_type]['variant'])) {
            $variant = $available_components[$item_type]['variant'];
            $content['variant'] = $variant[array_rand($variant)];
        }

        // Inversed mode of the element
        if (isset($available_components[$item_type]['mode']) && $available_components[$item_type]['mode'] == 'inversed') {
            // clear content
            unset($content);
            $content = [];
            if ($context['color_mode'] == 'light') {
                $palete = $decoration['global']['dark_palete'];
            }
            else {
                $palete = $decoration['global']['light_palete'];
            }

            $background = 'bg-' . $decoration['global']['secondary_color'] . '-' . $palete;
            $content['decoration']['background'] = $background;

            // re-render atoms TBD: avoid double rendering!

            foreach ($item['atoms'] as $key => $atom) {
            // render atoms
                $item['atoms'][$key]['mode'] = 'inversed';
                if (isset($content[$item['atoms'][$key]['type']])) {
                    if (is_array($content[$item['atoms'][$key]['type']])) {
                        $content[$item['atoms'][$key]['type']][] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                    }
                    else {
                        $temp = $content[$item['atoms'][$key]['type']];
                        unset($content[$item['atoms'][$key]['type']]);
                        $content[$item['atoms'][$key]['type']] = [];
                        $content[$item['atoms'][$key]['type']][] = $temp;
                        $content[$item['atoms'][$key]['type']][] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                    }

                }
                else {
                    $content[$item['atoms'][$key]['type']] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                }
            }
        }
        // Re-render image TBD: more universal
        if (isset($available_components[$item_type]["display"]["image"]) && $available_components[$item_type]["display"]["image"]) {
            foreach ($item['atoms'] as $key => $atom) {
                if ($item['atoms'][$key]['type'] == 'image') {
                    $item['atoms'][$key]['display'] = $available_components[$item_type]['display']['image'];
                    $content['image'] = parent::render($item['atoms'][$key], $decoration, $twigEnvironment, $context);
                }
            }
        }
        // Additional class
        if (isset($available_components[$item_type]["light_mode_class"]) && $context['color_mode'] == 'dark') {
            $content['additional_class'] = $available_components[$item_type]["light_mode_class"];
        }
        if (isset($available_components[$item_type]["dark_mode_class"]) && $context['color_mode'] == 'light') {
            $content['additional_class'] = $available_components[$item_type]["dark_mode_class"];
        }
        // TBD: find the reason why type is empty
        if ($item['type']) {
            $html = $twigEnvironment->render('components/items/' . $item['type'] . '.html.twig', [
                'content' => $content,
            ]);
        }
        else {
            $html = '';
        }
        return $html;
    }
}
