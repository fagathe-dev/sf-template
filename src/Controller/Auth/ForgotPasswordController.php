<?php 
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forgot-password', name: 'app_forgot_password_')]
final class ForgotPasswordController extends AbstractController
{
    
    #[Route('', name: 'request', methods: ['GET', 'POST'])]
    public function forgotPasswordRequest(): Response
    {
        return $this->render('auth/forgot-password/request.html.twig');
    }
}