<?php


namespace App\Controller\Site;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\WorkRepository;
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
    public function getWorks(WorkRepository $workRepository, CategoryRepository $categoryRepository)
    {
        // récupère toutes les données de la table work
        $categories = $categoryRepository->findAll();
        $works = $workRepository->findAll();
        return $this -> render('site/list_works.html.twig',
            [
                'categories' => $categories,
                'works' => $works
            ]
        );
    }

    /**
     * @Route("/artistes", name="get_artists")
     *
     * Liste tous les artistes sauf s'il y a une recherche par le champs du formulaire
     */
    public function getArtists(ArtistRepository $artistRepository, Request $request)
    {
        /* s'il existe une variable get pour le champs 'search' du form */
        if($request->query->get('search')){
            /* je récupère son contenu dans $name */
            $name = $request->query->get('search');
            /* je lance la recherche dans la base avec la méthode findByName() */
            $artists = $artistRepository->findByName($name);
        } else{
            // recherche tous les artistes [], order by 'name'
            $artists = $artistRepository -> findBy([],['name' => 'ASC']);
        }
        if(empty($artists)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('site/list_artists.html.twig',
            [
                'artists' => $artists,
                'display' => $display
            ]
        );
    }

    /**
     * @Route("/artistes/categorie/{id_category}/{category}", name="get_artists_bycategory")
     *
     * Liste des artistes par categorie
     * $id_category
     */
    public function getArtistsByCategory($id_category, $category, ArtistRepository $artistRepository, Request $request)
    {
        /* s'il existe une variable get pour le champs 'search' du form */
        if($request->query->get('search')){
            /* je récupère son contenu dans $name */
            $name = $request->query->get('search');
            /* je lance la recherche dans la base avec la méthode findByName() */
            $artists = $artistRepository->findByName($name, $id_category);
        } else{
            // recherche tous les artistes [], order by 'name'
            $artists = $artistRepository -> findByCategory($id_category);
        }
        if(empty($artists)){
            $display = false;
        } else {
            $display = true;
        }
        return $this -> render('site/list_artists.html.twig',
            [
                'artists' => $artists,
                'display' => $display,
                'id_category' => $id_category,
                'category' => $category,
                'page_category' => true
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
     * @Route("/works/{id}", name="card_work")
     */
    public function cardWork($id, WorkRepository $workRepository)
    {
        $work = $workRepository->find($id);
        return $this -> render('site/card_work.html.twig',
            [
                'work' => $work
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

    /**
     * @Route("/contact", name="contact_form")
     */
    public function contactForm(Request $request, \Swift_Mailer $mailer)
    {
        // je crée un nouveau contact
        $contact = new Contact();
        // je crée mon formulaire qui correspond à mon ContactType
        // et je lui passe en paramètre le contact
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $message = (new \Swift_Message('Nouveau message'))
                    ->setFrom($contact->getEmail())
                    ->setTo('virginie.meymat@lapiscine.pro')
                    ->setBody(
                        $this->renderView(
                            'site/_mail.html.twig', [
                                'prenom' => $contact->getFirstname(),
                                'nom' => $contact->getLastname(),
                                'message' => $contact->getMessage()
                            ]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
                $this->addFlash('success', 'Votre demande a bien été envoyée.');
                return $this->redirectToRoute('contact_form');
            }
        }

        return $this->render('site/contact.html.twig', [
            'formContact' => $form->createView()
        ]
        );
    }

    /**
     * @Route("/contact/{work}", name="contact_form_work")
     */
    public function contactFormOeuvre($work, Request $request, \Swift_Mailer $mailer)
    {
        // je crée un nouveau contact
        $contact = new Contact();
        // je crée mon formulaire qui correspond à mon ContactType
        // et je lui passe en paramètre le contact
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $message = (new \Swift_Message('Nouveau message sur '.$work))
                    ->setFrom($contact->getEmail())
                    ->setTo('virginie.meymat@lapiscine.pro')
                    ->setBody(
                        $this->renderView(
                            'site/_mail.html.twig', [
                                'prenom' => $contact->getFirstname(),
                                'nom' => $contact->getLastname(),
                                'message' => $contact->getMessage()
                            ]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
                $this->addFlash('success', 'Votre demande a bien été envoyée.');
                return $this->redirectToRoute('contact_form');
            }
        }

        return $this->render('site/contact.html.twig', [
            'formContact' => $form->createView(),
            'work' => $work
        ]
        );
    }

    /**
     * @Route("/exposition", name="show_exhibition")
     */
    public function showExhibition()
    {
        return $this->render('site/exhibition.html.twig');
    }
}