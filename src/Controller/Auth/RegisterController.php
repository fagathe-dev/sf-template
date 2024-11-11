<?php
namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\Auth\RegisterType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration', name: 'app_registration_')]
final class RegisterController extends AbstractController
{
    public function __construct(private UserService $service)
    {
    }

    #[Route(path: '', name: 'index', methods: ['GET', 'POST'])]
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->service->create($user)) {

                return $this->redirectToRoute('app_login');
            }

        }

        return $this->render('auth/registration/index.html.twig', compact('form'));
    }
}
