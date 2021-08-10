<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles_show")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();


        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * @Route("/article/new", name="create_article")
     * @Route("/article/{id}/update", name="update_article")
     */
    public function create(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$article) {
            $article = new Article();
        }

        $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('content')
            ->add('author')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->getForm();

        /* Lors de la réponse du formulaire, les données POST ou GET reçue sont bindée automatiquement avec $article */
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if ($article->getId()) {
                $article->setUpdatedAt(new \DateTimeImmutable());
            } else {
                $article->setCreatedAt(new \DateTimeImmutable());
                $article->setUpdatedAt(new \DateTimeImmutable());
            }

            // on prépare la requête
            $manager->persist($article);
            // on lance la requête
            $manager->flush();
            return $this->redirectToRoute('articles_show');
        }
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(Article $article)
    {
        return $this->render('article/article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/{id}/delete", name="article_delete")
     */
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute('articles_show');
    }
}
