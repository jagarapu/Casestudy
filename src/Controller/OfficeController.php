<?php

namespace App\Controller;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\SearchFilter;
use App\Form\Type\OfficeSearchType;
use App\Form\Type\OfficeType;
use App\Service\OfficeCapacityCheck;
use App\Service\OfficeEntryExistService;
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
    public function new(Request $request)
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
    public function edit(Request $request, Office $office)
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

        return $this->render('office/office_list.html.twig', [
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
        $user = $this->getUser();
        $officeEntryExistService->entryEmployeeOffice($user, $office);

        return $this->redirectToRoute('view_current_office');
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
    public function viewCurrentOffice(OfficeEntryExistService $officeEntryExistService, OfficeCapacityCheck $officeCapacityCheck)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $officeId = $em->getRepository(OfficeOccupancy::class)->getOffice($user);

        $office = null;
        $officeOccupancy = null;
        $officeOccupancyStatusByUser = null;

        if ($officeId) {
            $office = $em->getRepository(Office::class)->find($officeId[0]);
            $officeOccupancyIdResult = $em->getRepository(OfficeOccupancy::class)->findOccupiedOffice($user);
            if ($officeOccupancyIdResult) {
                $officeOccupancy = $em->getRepository(OfficeOccupancy::class)->find($officeOccupancyIdResult[0]);
            }
            $officeOccupancyStatusByUser = $em->getRepository(OfficeOccupancy::class)
                ->findOfficeOccupancyStatus($user, $office);
        }

        if (empty($office)) {
            return $this->render(
                'office/no_office_available.html.twig',
                [
                    'user' => $user,
                    'office' => $office,
                ]
            );
        }

        $flag = $officeEntryExistService->checkAlreadyEnterToOffice($user);
        $checkOfficeCapacity = $officeCapacityCheck->checkOfficeCapacity($office);
        $officeWiseOccupancy = $em->getRepository(OfficeOccupancy::class)->fetchOfficeOccupancyData();
        return $this->render(
            'office/current_office.html.twig',
            [
                'user' => $user,
                'office' => $office,
                'officeOccupancy' => $officeOccupancy,
                'checkOfficeCapacity' => $checkOfficeCapacity,
                'officeOccupancyStatus' => $officeOccupancyStatusByUser,
                'officeWiseOccupancy' => $officeWiseOccupancy,
                'flag' => $flag,
            ]
        );
    }

    /**
     * @Route("/{id}/office-view", name="office_view")
     */
    public function viewOffice(Office $office, OfficeEntryExistService $officeEntryExistService, OfficeCapacityCheck $officeCapacityCheck)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $officeOccupancyStatusByUser = $em->getRepository(OfficeOccupancy::class)
            ->findOfficeOccupancyStatus($user, $office);
        $officeOccupancy = null;
        $officeOccupancyIdResult = $em->getRepository(OfficeOccupancy::class)->findOccupiedOffice($user);
        if ($officeOccupancyIdResult) {
            $officeOccupancy = $em->getRepository(OfficeOccupancy::class)->find($officeOccupancyIdResult[0]);
        }
        $flag = $officeEntryExistService->checkAlreadyEnterToOffice($user);
        $checkOfficeCapacity = $officeCapacityCheck->checkOfficeCapacity($office);

        $officeWiseOccupancy = $em->getRepository(OfficeOccupancy::class)->fetchOfficeOccupancyData();
        
        return $this->render(
            'office/current_office.html.twig',
            [
                'user' => $user,
                'office' => $office,
                'officeWiseOccupancy' => $officeWiseOccupancy,
                'officeOccupancyStatus' => $officeOccupancyStatusByUser,
                'officeOccupancy' => $officeOccupancy,
                'checkOfficeCapacity' => $checkOfficeCapacity,
                'flag' => $flag,
            ]
        );
    }

    /**
     * Deletes a office entity.
     *
     * @Route("/office/{id}/delete", name="office_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Office $office, OfficeCapacityCheck $officeCapacityCheck)
    {
        $em = $this->getDoctrine()->getManager();
        $checkOfficeCapacityStatus = $officeCapacityCheck->checkOfficeCapacity($office);
        if ($checkOfficeCapacityStatus) {
            $em->remove($office);
            $em->flush();
            $this->get('session')->getFlashBag()->set(
                'flashSuccess',
                $office->getTitle() . ' office deleted successfully'
            );
        }
        $this->get('session')->getFlashBag()->set(
            'flashError',
            $office->getTitle() . ' this office is occupied with employees and then cannot be deleted.
             please ensure there are zero employees logged in this office'
        );

        return $this->redirectToRoute('office_list');
    }
}