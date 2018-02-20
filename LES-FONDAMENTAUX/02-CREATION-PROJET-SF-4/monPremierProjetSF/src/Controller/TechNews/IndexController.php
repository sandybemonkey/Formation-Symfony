<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Service\Article\ArticleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{
    /**
     * Page d'accueil de notre site
     * Configuration de notre route grâce dans routes.yaml
     * @param ArticleProvider $articleProvider
     * @return Response
     */
    public function index(ArticleProvider $articleProvider) {

        # Récupération des Articles depuis ArticleProvider
        $articles = $articleProvider->getArticles();

        # Transmission à la vue
        return $this->render('index/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * Page permettant d'afficher les articles d'une catégorie
     * @Route("/categorie/{libellecategorie}",
     *     name="index_categorie",
     *     methods={"GET"},
     *     requirements={"libellecategorie"="\w+"})
     * @param $libellecategorie
     * @return Response
     */
    public function categorie($libellecategorie = 'tout') {
        return $this->render('index/categorie.html.twig');
    }

    /**
     * Page permettant d'afficher un article
     * @see https://symfony.com/doc/current/routing.html#adding-wildcard-requirements
     * @Route("/{libellecategorie}/{slugarticle}_{id}.html", name="index_article",
     *     requirements={"idarticle"="\d+"} )
     * @param Article $article
     * @return Response
     */
    public function article(Article $article) {
        # https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-paramconverter
        # index.php/business/une-formation-symfony-a-paris_2.html

        # Récupération avec Doctrine
        # $article = $this->getDoctrine()
        #     ->getRepository(Article::class)
        #     ->find($idarticle);

        # Si aucun article n'est trouvé...
        if (!$article) {
            # On génère une exception
            # throw $this->createNotFoundException(
            #     'Nous n\'avons pas trouvé votre article ID : '.$idarticle
            # );
            # Ou on peut aussi rediriger l'utilisateur sur la page index.
            return $this->redirectToRoute('index',[],Response::HTTP_MOVED_PERMANENTLY);
        }

        /**
         * Lazy Loading et le Chargement des Related Objects
         * Il est important de comprendre que nous avons accès à l'objet catégorie du produit,
         * de façon AUTOMATIQUE ! Cependant, les données de la catégorie ne sont récupéré
         * par doctrine que lorsque nous en faisant la demande, et pas avant ! Ceci pour alléger
         * le chargement de votre page.
         */
        # $categorie = $article->getCategorie()->getLibelle();

        return $this->render('index/article.html.twig', [
            'article' => $article,
            #   'categorie' => $categorie
        ]);
    }

}