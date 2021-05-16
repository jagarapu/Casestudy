<?php

namespace App\Controller;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\SearchFilter;
use App\Entity\User;
use App\Form\Type\OfficeSearchType;
use App\Form\Type\OfficeType;
use App\Service\OfficeEntryExistService;
use App\Service\UserManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @Security("is_granted('ROLE_ADMIN')")
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

            return $this->redirectToRoute('office_list');
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
    public function list(Request $request, PaginatorInterface $paginator)
    {
        $officeSearch = new SearchFilter();
        $em = $this->getDoctrine()->getManager();
        $searchForm = $this->createForm(OfficeSearchType::class, $officeSearch);
        $searchForm->handleRequest($request);

        $offices = $em->getRepository(Office::class)->findAll();
        $officeWiseOccupancy = $em->getRepository(OfficeOccupancy::class)->fetchOfficeOccupancyData();
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $offices = $em->getRepository(Office::class)
                ->officeSearchByCity($officeSearch);
        }
        $pagination = $paginator->paginate(
            $offices,
            $request->query->getInt('page', 1), /*page number*/
            SearchFilter::OBJECT_PER_PAGE /*limit per page*/
        );

        return $this->render('office/view_office.html.twig', [
            'offices' => $pagination,
            'officeWiseOccupancy' => $officeWiseOccupancy,
            'form' => $searchForm->createView(),
        ]);
    }

    /**
     * Creates a office entry.
     *
     * @Route("/{id}/entry", name="office_entry")
     */
    public function entryOffice(Office $office, OfficeEntryExistService $officeEntryExistService)
    {
        $officeOccupancy = $officeEntryExistService->entryOffice($office);

        return $this->render(
            'office/current_office.html.twig',
            [
                'user' => $officeOccupancy->getUser(),
                'office' => $officeOccupancy->getOffice(),
                'officeOccupancy' => $officeOccupancy,
            ]
        );
    }

    /**
     * Creates a office entry.
     *
     * @Route("/{id}/exit", name="office_exit")
     */
    public function exitOffice(OfficeOccupancy $officeOccupancy, OfficeEntryExistService $officeEntryExistService)
    {
        $officeEntryExistService->exitOffice($officeOccupancy);

        return $this->redirectToRoute('office_list');
    }

    /**
     * @Route("/view-current-office", name="view_current_office")
     */
    public function viewCurrentOffice()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $officeId = $em->getRepository(OfficeOccupancy::class)->getOffice($user);
        $office = null;
        $officeOccupancy = null;
        if ($officeId) {
            $office = $em->getRepository(Office::class)->find($officeId);
            $officeOccupancy = $em->getRepository(OfficeOccupancy::class)->findOneBy(['user' => $user, 'office' => $office]);
        }

        return $this->render(
            'office/current_office.html.twig',
            [
                'user' => $user,
                'office' => $office,
                'officeOccupancy' => $officeOccupancy,
            ]
        );
    }

    /**
     * @Route("/{id}/office-view", name="office_view")
     */
    public function viewOffice(Office $office)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $officeOccupancyStatusByUser = $em->getRepository(OfficeOccupancy::class)
            ->findOfficeOccupancyStatus($user, $office);

        return $this->render(
            'office/current_office.html.twig',
            [
                'user' => $user,
                'office' => $office,
                'officeOccupancyStatus' => ($officeOccupancyStatusByUser ? $officeOccupancyStatusByUser[0] : $officeOccupancyStatusByUser)
            ]
        );
    }

    /**
     * Deletes a office entity.
     *
     * @Route("/office/{id}/delete", name="office_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Office $office)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($office);
        $em->flush();
        $this->get('session')->getFlashBag()->set(
            'flashSuccess',
            $office->getTitle() . ' office deleted successfully'
        );

        return $this->redirectToRoute('office_list');
    }
}