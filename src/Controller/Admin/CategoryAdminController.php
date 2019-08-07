<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryAdminController extends AbstractController
{
    /**
     * @Route("/admin/category/insert", name="category_form_insert")
     */
    public function categoryFormInsert(Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe Category
        $category = new Category();
        // je crée un nouveau formulaire pour l'entité Category
        $form = $this->createForm(CategoryType::class, $category);
        // je crée une vue du formulaire
        $formCategoryView = $form->createView();

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // on enregistre l'entité créée
                $entityManager->persist($category);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                // message flash de validation
                $this->addFlash('Success', 'L\'enregistrement a bien été effectué');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        }
        return $this->render('admin/category/category_form.html.twig',
            [
                'formCategoryView' => $formCategoryView
            ]
        );
    }

    /**
     * @Route("/admin/category/update/{id}", name="category_form_update")
     */
    public function categoryFormUpdate($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // je crée une instance de la classe Category
        $category = $categoryRepository->find($id);
        // je crée un nouveau formulaire pour l'entité Category
        $form = $this->createForm(CategoryType::class, $category);
        // je crée une vue du formulaire
        $formCategoryView = $form->createView();

        if ($request->isMethod('post')) {
            // je récupère les données du form
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // on enregistre l'entité créée
                $entityManager->persist($category);
                // on envoie la requête vers la bdd
                $entityManager->flush();

                return $this->redirectToRoute('list_categories');
            } else {
                $this->addFlash('Fail', 'L\'enregistrement n\'a pas été effectué. Veuillez réessayer.');
            }
        } else {
            return $this->render('admin/category/category_form.html.twig',
                [
                    'formCategoryView' => $formCategoryView
                ]
            );
        }

    }

    /**
     * @Route("/admin/category/delete/{id}", name="category_delete")
     *
     * supprime un enregistrement dans la table category
     */
    public function removeCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager){
        // je récupère la catégorie(entité) dont l'id est celui de la wildcard
        $category = $categoryRepository->find($id);

        // Signale à Doctrine qu'on veut supprimer l'entité en argument de la base de données
        $entityManager->remove($category);
        // Met à jour la base à partir des objets signalés à Doctrine.
        // Tant que cette méthode n'est pas appellée, rien n'est modifié en base.
        $entityManager->flush();

        return $this->redirectToRoute('list_categories');
    }
}