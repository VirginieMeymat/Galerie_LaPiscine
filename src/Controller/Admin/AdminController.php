<?php


namespace App\Controller\Admin;


use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function listWorks()
    {
        return $this -> render('admin/work/list_works.html.twig');
    }

    /**
     * @Route("/admin/artists", name="list_artists")
     */
    public function listArtists(ArtistRepository $artistRepository)
    {
        $artists = $artistRepository->findAll();
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