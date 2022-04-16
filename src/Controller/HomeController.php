<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Service\Content;
use App\Service\Decoration;
use App\Service\Section;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     *
     * index
     *
     * @param  mixed $content
     * @param  mixed $component
     * @param  mixed $twigEnvironment
     * @return Response
     */
    public function index(Content $content, Decoration $decoration_obj, Section $component, Environment $twigEnvironment): Response
    {
        $data = $content->load();
        $decoration = $decoration_obj->load();
        // $output = print_r($data, true);
        $output = '';
        $counter = 1;
        $length = count($data['sections']);
        foreach ($data['sections'] as $key => $section) {
                $section['order'] = $counter;
                $section['length'] = $length;
                if (!isset($section['color_mode']) || !$section['color_mode']) {
                    if ($counter == $length || $counter == 2) {
                        if ($data['sections'][$key - 1]['color_mode'] == 'light') {
                            $section['color_mode'] = 'dark';
                        }
                        else {
                            $section['color_mode'] = 'light';
                        }
                    }
                    else {
                        $color_mode = ['light', 'dark'];
                        $section['color_mode'] = $color_mode[array_rand($color_mode)];
                    }
                    $data['sections'][$key]['color_mode'] = $section['color_mode'];
                }
                $output .= $component->render($section, $decoration, $twigEnvironment, $section);
                $counter++;
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'Design Management System',
            'markup' => $output,
        ]);
    }
}
