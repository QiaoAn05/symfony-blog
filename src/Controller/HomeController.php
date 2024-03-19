<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)->findAll();
        $article = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $article,
        ]);
    }
}
