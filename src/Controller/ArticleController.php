<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // #[Route('/article', name: 'app_article')]
    public function index(EntityManagerInterface $entityManager ,int $id): Response
    {

        $arcticle = $entityManager->getRepository(Article::class)->findBy(['id' => $id]);

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    public function edit(EntityManager $entityManager, Request $request, int $id=null): Response
    {
        if($id) {
            $mode = "update";
            $article = $entityManager->getRepository(Article::class)->findBy(['id' => $id]); 
        }
        else {
            $mode = "new";
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::Class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->saveArcticle($article, $mode);

            return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
        }

        $parameters = array(
            'form' => $form,
            'article' => $article,
            'mode' => $mode
        );

        return $this->render('article/edit.html.twig', $parameters);
    }

    public function remove(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->findBy(['id' => $id])[0];

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    private function completeArticleBeforeSave(Article $article, string $mode) {
        if($article->getIsPublished()){
            $article->setPublishedAt(new \DateTime());
        }
        $article->setAuthor($this->getUser());

        return $article;
    }

    public function saveArticle(EntityManagerInterface $entityManager, Article $article, string $mode)
    {
        $article = $this->completeArticleBeforeSave($article, $mode);
        
        // $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        $this->addFlash('success', 'Enregistré avec succès');
    }
}
