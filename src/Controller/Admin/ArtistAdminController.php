<?php


namespace App\Controller\Admin;


use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArtistAdminController extends AbstractController
{
    /**
     * @Route("/admin/artist/insert", name="artist_form_insert")
     */
    public function artistFormInsert(Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe artist
        $artist = new Artist();
        // je crée un nouveau formulaire pour l'entité artist
        $form = $this->createForm(ArtistType::class, $artist);

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                /* upload d'un fichier */
                /** @var UploaderFile $file */
                  $file = $form['image']->getData();

                  // this condition is needed because the 'brochure' field is not required
                  // so the PDF file must be processed only when a file is uploaded
                  /*if ($file) {
                      $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                      // this is needed to safely include the file name as part of the URL
                      $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                      $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                      // Move the file to the directory where brochures are stored
                      try {
                          $file->move(
                              $this->getParameter('brochures_directory'),
                              $newFilename
                          );
                      } catch (FileException $e) {
                          // ... handle exception if something happens during file upload
                      }*/

                      // updates the 'brochureFilename' property to store the PDF file name
                      // instead of its contents
                      /*$artist->setBrochureFilename($newFilename);*/

                    //// fin upload
                  

                // on enregistre l'entité créée
                $entityManager->persist($artist);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                // message flash de validation
                $this->addFlash('Success', 'L\'enregistrement a bien été effectué');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        }
        return $this->render('admin/artist/artist_form.html.twig',
            [
                'formArtistView' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/artist/update/{id}", name="artist_form_update")
     */
    public function artistFormUpdate($id, ArtistRepository $artistRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe Artist
        $artist = $artistRepository->find($id);
        // je crée un nouveau formulaire pour l'entité Artist
        $form = $this->createForm(ArtistType::class, $artist);
        // je crée une vue du formulaire

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploaderFile $file */
                $file = $form['image']->getData();

                if ($file) {
                    // récup du nom du fichier d'origine (sans l'extension)
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // sécurité: traite la chaine de caractères et la met en minuscules
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    // recrée le nom du fichier avec son extension d'origine
                    $newFilename = $safeFilename.'.'.$file->getClientOriginalExtension();

                    // Bouge le fichier dans le répertoire où sont stockés les images
                    // artist_directory est paramétré dans config/services.yaml
                    try {
                        $file->move(
                            $this->getParameter('artist_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $artist->setImage($newFilename);
                }
                // on enregistre l'entité créée
                $entityManager->persist($artist);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                return $this->redirectToRoute('list_artists');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        } else {
            return $this->render('admin/artist/artist_form.html.twig',
                [
                    'formArtistView' => $form->createView()
                ]
            );
        }

    }

    /**
     * @Route("/admin/artist/delete/{id}", name="artist_delete")
     *
     * supprime un enregistrement dans la table artist
     */
    public function removeArtist($id, ArtistRepository $artistRepository, EntityManagerInterface $entityManager){
        // je récupère la catégorie(entité) dont l'id est celui de la wildcard
        $artist = $artistRepository->find($id);

        // Signale à Doctrine qu'on veut supprimer l'entité en argument de la base de données
        $entityManager->remove($artist);
        // Met à jour la base à partir des objets signalés à Doctrine.
        // Tant que cette méthode n'est pas appellée, rien n'est modifié en base.
        $entityManager->flush();

        return $this->redirectToRoute('list_artists');
    }
}