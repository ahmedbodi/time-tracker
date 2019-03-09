<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\LeaveEntitlement;
use App\Form\LeaveEntitlementType;
use App\Repository\LeaveEntitlementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/leave/entitlement")
 */
class LeaveEntitlementController extends AbstractController
{
    /**
     * @Route("/", name="leave_entitlement_index", methods={"GET"})
     */
    public function index(LeaveEntitlementRepository $leaveEntitlementRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('leave_entitlement/index.html.twig', [
            'leave_entitlements' => $leaveEntitlementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user", name="user_entitlement_view", methods={"GET"})
     */
    public function showUserEntitlement(Security $security)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $security->getUser();
	$entitlement = $user->getLeaveEntitlement();
	// Save the entitlement just incase the user doesnt have an entitlement yet
	$this->getDoctrine()->getManager()->persist($entitlement);
	$this->getDoctrine()->getManager()->flush();
        return $this->render('leave_entitlement/show.html.twig', [
            'leave_entitlement' => $user->getLeaveEntitlement()
        ]);
    }

    /**
     * @Route("/{id}", name="leave_entitlement_show", methods={"GET"})
     */
    public function show(LeaveEntitlement $leaveEntitlement): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('leave_entitlement/show.html.twig', [
            'leave_entitlement' => $leaveEntitlement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="leave_entitlement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, LeaveEntitlement $leaveEntitlement, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(LeaveEntitlementType::class, $leaveEntitlement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('leave_entitlement_index', [
                'id' => $leaveEntitlement->getId(),
            ]);
        }

        return $this->render('leave_entitlement/edit.html.twig', [
            'leave_entitlement' => $leaveEntitlement,
            'form' => $form->createView(),
        ]);
    }
}
