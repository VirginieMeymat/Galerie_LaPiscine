<?php


namespace App\Controller\Admin;


use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WorkAdminController extends AbstractController
{
    /**
     * @Route("/admin/work/insert", name="work_form_insert")
     */
    public function workFormInsert(Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe work
        $work = new Work();
        // je crée un nouveau formulaire pour l'entité work
        $form = $this->createForm(WorkType::class, $work);

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                /* upload d'un fichier */
                /** @var UploadedFile $file */
                  $file = $form['image']->getData();

                  // this condition is needed because the 'brochure' field is not required
                  // so the PDF file must be processed only when a file is uploaded
                if ($file) {
                    // récup du nom du fichier d'origine (sans l'extension)
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // sécurité: traite la chaine de caractères et la met en minuscules
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    // recrée le nom du fichier avec son extension d'origine
                    $newFilename = $safeFilename.'.'.$file->getClientOriginalExtension();

                    // Bouge le fichier dans le répertoire où sont stockés les images
                    // work_directory est paramétré dans config/services.yaml
                    try {
                        $file->move(
                            $this->getParameter('work_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $work->setImage($newFilename);
                }
                // on enregistre les modifications
                $entityManager->persist($work);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                // message flash de validation
                $this->addFlash('Success', 'L\'enregistrement a bien été effectué');
                return $this->redirectToRoute('work_form_insert');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        }
        return $this->render('admin/work/work_form.html.twig',
            [
                'formWorkView' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/work/update/{id}", name="work_form_update")
     */
    public function workFormUpdate($id, WorkRepository $workRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe work
        $work = $workRepository->find($id);
        // je crée un nouveau formulaire pour l'entité work
        $form = $this->createForm(WorkType::class, $work);

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $file */
                $file = $form['image']->getData();

                if ($file) {
                    // récup du nom du fichier d'origine (sans l'extension)
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // sécurité: traite la chaine de caractères et la met en minuscules
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    // recrée le nom du fichier avec son extension d'origine
                    $newFilename = $safeFilename.'.'.$file->getClientOriginalExtension();

                    // Bouge le fichier dans le répertoire où sont stockés les images
                    // work_directory est paramétré dans config/services.yaml
                    try {
                        $file->move(
                            $this->getParameter('work_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $work->setImage($newFilename);
                }
                // on enregistre les modifications
                $entityManager->persist($work);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                return $this->redirectToRoute('list_works');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        } else {
            return $this->render('admin/work/work_form.html.twig',
                [
                    // je crée une vue du formulaire et la passe en variable pour le fichier twig
                    'formWorkView' => $form->createView(),
                    'work' => $work
                ]
            );
        }

    }

    /**
     * @Route("/admin/work/delete/{id}", name="work_delete")
     *
     * supprime un enregistrement dans la table work
     */
    public function removework($id, workRepository $workRepository, EntityManagerInterface $entityManager){
        // je récupère la catégorie(entité) dont l'id est celui de la wildcard
        $work = $workRepository->find($id);

        // Signale à Doctrine qu'on veut supprimer l'entité en argument de la base de données
        $entityManager->remove($work);
        // Met à jour la base à partir des objets signalés à Doctrine.
        // Tant que cette méthode n'est pas appellée, rien n'est modifié en base.
        $entityManager->flush();

        return $this->redirectToRoute('list_works');
    }
}