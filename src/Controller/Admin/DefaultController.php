<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_default_')]
final class DefaultController extends AbstractController
{


    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('layouts/admin.html.twig');
    }
}
