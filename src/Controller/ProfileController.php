<?php

namespace App\Controller;

use App\Entity\AnnonceListByUser;
use App\Repository\AnnonceListByUserRepository;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(ComputerRepository $computerRepository, AnnonceListByUserRepository $annonceListByUserRepository): Response
    {
        $author = $this->getUser();
        return $this->render('profile/index.html.twig', [
            'computers' => $computerRepository->findBy([
                'author' => $author
            ]),
            'annoncesFav' =>$annonceListByUserRepository->findBy([
                'users' => $author
            ])
        ]);
    }
}
