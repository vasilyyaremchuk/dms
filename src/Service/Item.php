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

        $available_components = [];
        $path    = '../templates/components/items/';
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            $parts = explode('.', $file);
            if ($parts[1] == 'json') {
                $available_components[$parts[0]] = json_decode(file_get_contents($path . $file), true);
            }
        }
        foreach ($available_components as $key => $available_component) {
            $available_components[$key]['available_elements'] = array_keys($available_component['elements']);
            $available_components[$key]['score'] = 0;
        }
        $content = [];
        // select item type
        $available_components['score'] = 0;
        foreach ($item['atoms'] as $key => $atom) {
            $item['atoms'][$key] = parent::atom_type_validate($atom);
            foreach ($available_components as $av_key => $available_component) {
                if (isset($available_component['available_elements']) && is_array($available_component['available_elements'])) {
                    if(in_array($item['atoms'][$key]['type'], $available_component['available_elements'])) {
                        $available_components[$av_key]['score'] += 1;
                    }
                    else {
                        $available_components[$av_key]['score'] -= 1;
                    }
                }
            }
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
        $item_type = '';
        $item_type_score = 0;
        foreach ($available_components as $key => $available_component) {
            if (is_array($available_component) && $available_component['score'] > $item_type_score) {
                $item_type_score = $available_component['score'];
                $item_type = $key;
            }
        }
        if (!empty($available_components[$item_type]['variant'])) {
            $variant = $available_components[$item_type]['variant'];
            $content['variant'] = $variant[array_rand($variant)];
        }
        // TBD: validate if item type is set
        $item['type'] = $item_type;

        $html = $twigEnvironment->render('components/items/' . $item['type'] . '.html.twig', [
            'content' => $content,
        ]);
        return $html;
    }
}
