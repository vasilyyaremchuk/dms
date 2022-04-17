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
        $output = '';
        if (isset($_GET['industry']) && $_GET['industry']) {
            $data = $content->load($_GET['industry']);
            $decoration = $decoration_obj->load($_GET['industry']);

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
        }
        else {
            $output .= $this->get_form();
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'Design Management System',
            'markup' => $output,
        ]);
    }

    public function get_form() {
        return '<!-- Form -->
        <section class="bg-white w-full flex flex-col justify-center items-center pt-14 pb-16">
          <div class="container mx-auto flex flex-col items-center pb-8">
            <h1 class="w-full my-2 text-5xl font-bold leading-tight text-center text-gray-800">
              Manage your Web application design!
            </h1>
            <form class="w-3/4 mx-auto px-12" method="get" action="/">
              <label for="industry" class="inline-flex mb-4 mt-8 text-2xl text-gray-800 space-y-8">
                Industry
              </label>
              <select name="industry" class="block w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-primary focus:border-gray-500">
                <option>Default</option>
                <option>Clinic</option>
                <option>Personal</option>
                <option>Restaurant</option>
              </select>
              <input type="submit" name="action" value="Generate" class="mx-auto lg:mx-0 hover:underline hover:bg-red-primary bg-black text-white font-bold rounded-full py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out space-y-8 mt-8" />
            </form>
          </div>
        </section>';
      }
}
