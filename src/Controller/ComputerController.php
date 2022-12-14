<?php

namespace App\Controller;

use App\Entity\AnnonceListByUser;
use App\Entity\Computer;
use App\Form\ComputerType;
use App\Repository\AnnonceListByUserRepository;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/computer')]
class ComputerController extends AbstractController
{
    #[Route('/', name: 'app_computer_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ComputerRepository $computerRepository): Response
    {
        return $this->render('computer/index.html.twig', [
            'computers' => $computerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_computer_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ComputerRepository $computerRepository): Response
    {
        $author = $this->getUser();
        $computer = new Computer();
        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $computer->setAuthor($author);
            $computer->setIsVisible(true);
            $sin = "";
            $letters = range('A', 'Z');
            for($i = 1; $i < 4; $i++){
                shuffle($letters);
                $sin .= array_shift($letters);
            }
            for($i = 1; $i < 3; $i++){
                $sin .= rand(0,9);
            }
            $computer->setSin($sin);

            $computerRepository->save($computer, true);
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('computer/new.html.twig', [
            'computer' => $computer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_computer_show', methods: ['GET'])]
    public function show(Computer $computer): Response
    {
        return $this->render('computer/show.html.twig', [
            'computer' => $computer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_computer_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request, Computer $computer, ComputerRepository $computerRepository): Response
    {
        $thisComputer = $computerRepository->find($id);
        
        if($thisComputer->getIsVisible() == false) {
            $this->addFlash('Erreur', "Cet ordinateur n'existe plus !");
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);
        $author = $this->getUser();

        if($author === false) {
            $this->addFlash('Erreur', "Vous devez avoir un compte pour ajouter/éditer un ordinateur");
            return $this->redirectToRoute('home');
        }
        
        if($this->container->get('security.authorization_checker')->IsGranted('ROLE_ADMIN') || $computer->getAuthor() == $author) {
            if ($form->isSubmitted() && $form->isValid()) {
                $computerRepository->save($computer, true);
                $this->addFlash('Succès', 'Votre ordinateur a bien été enregistré !');
                if($author->getRoles() == 'ROLE_ADMIN')
                return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            $this->addFlash('Erreur', "Vous devez être l'auteur pour pouvoir éditer une annonce d'ordinateur");
            return $this->redirectToRoute('home');
        }
        
        return $this->renderForm('computer/edit.html.twig', [
            'computer' => $computer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_computer_delete', methods: ['POST'])]
    public function delete(Request $request, Computer $computer, ComputerRepository $computerRepository): Response
    {
        $author = $this->getUser();

        if(!$author){
            $this->addFlash('Erreur', 'Vous devez avoir un compte pour pouvoir supprimer un ordinateur');
            return $this->redirectToRoute('home');
        }
        if($this->container->get('security.authorization_checker')->IsGranted('ROLE_ADMIN') || $computer->getAuthor() == $author) {
            $computer->setIsVisible(false);
            $computerRepository->save($computer);
        } else {
            $this->addFlash('Erreur', "Vous n'êtes pas l'auteur de cette annonce !");
            return $this->redirectToRoute('home', ['id'=> $computer->getId()]);
        }
        $this->addFlash('Succès', "L'ordinateur a bien été supprimée !");

        if($author->getRoles() == 'ROLE_ADMIN')
            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/fav', name: 'app_computer_fav', methods: ['GET', 'POST'])]
    public function favUserAnnonces(Computer $computer, AnnonceListByUserRepository $annonceListByUserRepository): Response
    {
       $user = $this->getUser();
       if(!$user) return $this->redirectToRoute('app_login');

        if($computer->isUserFav($user)){
            $signedUp = $annonceListByUserRepository->findOneBy([
                'computers' => $computer,
                'users' => $user
            ]);
            $annonceListByUserRepository->remove($signedUp);
            $this->addFlash('Erreur', "Cette annonce n'est plus dans vos favoris !");
            return $this->redirectToRoute('home');
        }

        $newFav = new AnnonceListByUser();
        $newFav ->setComputers($computer)
                ->setUsers($user);

        $annonceListByUserRepository->save($newFav);
        $this->addFlash('Succès', "Cette annonce est désormais dans vos favoris !");

        return $this->redirectToRoute('home');
    }
}
