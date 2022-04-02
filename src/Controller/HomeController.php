<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Service\Content;
use App\Service\Atom;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Content $content, Atom $component, Environment $twigEnvironment): Response
    {
        $data = $content->load();
        // $output = print_r($data, true);
        $output = '';
        foreach ($data['sections'][0]['items'][0]['atoms'] as $atom) {
            $output .= $component->render($atom, $twigEnvironment);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'Design Management System',
            'markup' => $output,
        ]);
    }
}
