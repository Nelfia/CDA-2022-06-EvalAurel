<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ComputerRepository $computerRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'computers' => $computerRepository->findBy([
                'isVisible' => true
            ])
        ]);
    }
    #[Route('/card/{id}', name: 'brand', methods: ['GET'])]
    public function brand(Brand $brand, ComputerRepository $computerRepository): Response
    {
        return $this->render('main/brand.html.twig', [
            'brand' => $brand,
            'computers' => $computerRepository->findAll()
        ]);
    }
}
