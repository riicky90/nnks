<?php

namespace App\Controller;

use Craue\ConfigBundle\Util\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'settings_index')]
    public function index()
    {
        return $this->render('settings/index.html.twig');
    }

    #[Route('/settings/update', name: 'settings_edit', methods: ['POST'])]
    public function edit(Request $request, Config $config)
    {
        $setting = $request->toArray();

        $config->set($setting["setting"], $setting["value"]);

        return new Response('success');
    }
}