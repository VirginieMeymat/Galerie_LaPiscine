<?php


namespace App\Controller\Admin;


use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\WorkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function adminHome()
    {
        return $this -> render('admin/home.html.twig');
    }

    /**
     * @Route("/admin/works", name="list_works")
     */
    public function listWorks(WorkRepository $workRepository, Request $request, PaginatorInterface $paginator)
    {
        // récupère toutes les données de la table artist
        $workQuery = $workRepository->createQueryBuilder('w')
            ->getQuery();
        // Paginer les résultats de la requête
        $works = $paginator->paginate(
        // Doctrine Query, not results
            $workQuery,
            // Definie le paramètre page
            $request->query->getInt('page', 1),
            // Nombre d'éléments par page
            5
        );
        if(empty($works)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('admin/work/list_works.html.twig',
            [
                'display' => $display,
                'works' => $works
            ]
        );
    }

    /**
     * @Route("/admin/artists", name="list_artists")
     */
    public function listArtists(ArtistRepository $artistRepository, Request $request, PaginatorInterface $paginator)
    {
        // récupère toutes les données de la table artist
        $artistsQuery = $artistRepository->createQueryBuilder('c')
            ->getQuery();
        // Paginer les résultats de la requête
        $artists = $paginator->paginate(
        // Doctrine Query, not results
            $artistsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );
        if(empty($artists)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('admin/artist/list_artists.html.twig',
            [
                'display' => $display,
                'artists' => $artists
            ]
        );
    }

    /**
     * @Route("/admin/categories", name="list_categories")
     */
    public function listCategories(CategoryRepository $categoryRepository)
    {
        // j'utilise la méthode findAll du repository pour récupérer toutes les Categories de la bdd
        $categories = $categoryRepository->findAll();

        if(empty($categories)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('admin/category/list_categories.html.twig',
            [
                'display' => $display,
                'categories' => $categories
            ]
        );
    }
}