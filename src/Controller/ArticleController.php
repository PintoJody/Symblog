<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleFormType;
use Symfony\Component\Security\Core\Security;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/create', name: 'create_article', methods: ['GET', 'POST'])]
    public function create(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $user = $this->security->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTimeImmutable('now'));
            $article->setRelation($user);
            $articleRepository->add($article);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/index.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }
}
