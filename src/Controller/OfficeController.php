<?php

namespace App\Controller;

use App\Entity\Office;
use App\Form\Type\OfficeType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OfficeController
 * @package App\Controller
 * @Route("techm/office")
 */
class OfficeController extends AbstractController
{
    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="office_new")
     */
    public function new(Request $request, UserManager $userManager)
    {
        $office = new Office();
        $form = $this->createForm(OfficeType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($office);
            $em->flush();

            return $this->redirectToRoute('home_page');
        }

        return $this->render('office/add_edit.html.twig', [
            'office' => $office,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="office_edit")
     */
    public function edit(Request $request, Office $office, UserManager $userManager)
    {
        $editForm = $this->createForm(OfficeType::class, $office);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('office_list');
        }

        return $this->render('office/add_edit.html.twig', [
            'office' => $office,
            'form' => $editForm->createView()
        ]);
    }

    /**
     * Lists all user entities.
     *
     * @Route("/list", name="office_list")
     */
    public function list(UserManager $userManager)
    {
        $em = $this->getDoctrine()->getManager();
        $offices = $em->getRepository(Office::class)->findAll();

        return $this->render('office/list.html.twig', [
            'offices' => $offices,
        ]);
    }

    /**
     * Activates/Deactiavtes a office entity.
     *
     * @Route("/{id}/toggle/activation", name="office_toggle_activation")
     */
    public function toggleActivation(Request $request, Office $office)
    {
        $em = $this->getDoctrine()->getManager();
        $office->setIsEnabled(!($office->getIsEnabled()));
        $em->flush();
        if($office->getIsEnabled()){
            $message = $office->getUsername() . ' user activated successfully';
        } else {
            $message = $office->getUsername() . ' user deactivated successfully';
        }

        return $this->redirectToRoute('office_list');
    }

}