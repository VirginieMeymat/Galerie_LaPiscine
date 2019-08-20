<?php


namespace App\Controller\Site;


use App\Repository\ArtistRepository;
use App\Repository\WorkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function homePage(){
        return $this -> render('site/home.html.twig');
    }

    /**
     * @Route("/works", name="get_works")
     */
    public function getWorks(WorkRepository $workRepository, Request $request, PaginatorInterface $paginator)
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
            20
        );
        if(empty($works)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('site/list_works.html.twig',
            [
                'display' => $display,
                'works' => $works
            ]
        );
    }

    /**
     * @Route("/artists", name="get_artists")
     */
    public function getArtists(ArtistRepository $artistRepository, Request $request, PaginatorInterface $paginator)
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
        return $this -> render('site/list_artists.html.twig',
            [
                'display' => $display,
                'artists' => $artists
            ]
        );
    }

    /**
     * @Route("/artists/{id}", name="card_artist")
     */
    public function cardArtist($id, ArtistRepository $artistRepository)
    {
       $artist = $artistRepository->find($id);
        return $this -> render('site/card_artist.html.twig',
            [
                'artist' => $artist
            ]
        );
    }

    /**
     * @Route("/mentions-legales", name="legal_notice")
     */

    public function legalNotice()
    {
        return $this -> render('site/legal_notice.html.twig');
    }
}