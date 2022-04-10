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
        foreach ($data['sections'] as $section) {
                $output .= $component->render($section, $decoration, $twigEnvironment, $section);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'Design Management System',
            'markup' => $output,
        ]);
    }
}
