<?php

namespace App\Controller;

use App\Entity\LeaveRequest;
use App\Form\LeaveRequestType;
use App\Repository\LeaveRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/leave/request")
 */
class LeaveRequestController extends AbstractController
{
    /**
     * @Route("/", name="leave_request_index", methods={"GET"})
     */
    public function index(LeaveRequestRepository $leaveRequestRepository, Security $security): Response
    {
	// Block access by anyone who isnt signed in
        $this->denyAccessUnlessGranted('ROLE_USER');
	$leaveRequests = $leaveRequestRepository->findAll();

	// If the user isnt an admin then only let them see their requests
	if (!in_array('ROLE_ADMIN', $security->getUser()->getRoles())) {
	    $leaveRequests = $security->getUser()->getLeaveRequests();
	}

        return $this->render('leave_request/index.html.twig', [
            'leave_requests' => $leaveRequestRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="leave_request_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $leaveRequest = new LeaveRequest();
	$leaveRequest->setUser($security->getUser());
	$formOptions = [
		'admin' => in_array('ROLE_ADMIN', $security->getUser()->getRoles()),
	];
        $form = $this->createForm(LeaveRequestType::class, $leaveRequest, $formOptions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($leaveRequest);
            $entityManager->flush();

            return $this->redirectToRoute('leave_request_index');
        }

        return $this->render('leave_request/new.html.twig', [
            'leave_request' => $leaveRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="leave_request_show", methods={"GET"})
     */
    public function show(LeaveRequest $leaveRequest): Response
    {
	$this->denyAccessUnlessGranted(['ROLE_USER', 'ROLE_ADMIN']);
        return $this->render('leave_request/show.html.twig', [
            'leave_request' => $leaveRequest,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="leave_request_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, LeaveRequest $leaveRequest, Security $security): Response
    {

	if (!$security->getUser() == $leaveRequest->getUser() ||
	    !in_array('ROLE_ADMIN', $securty->getUser()->getRoles())) {
		// Deny access to anyone who doesnt own this leave request or is not an admin
        	throw $this->createNotFoundException('The page could not be found');
	}

        $formOptions = [
                'admin' => in_array('ROLE_ADMIN', $security->getUser()->getRoles()),
        ];
        $form = $this->createForm(LeaveRequestType::class, $leaveRequest, $formOptions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	    // When the user modifies the request then un-approve it
	    if ($security->getUser() == $leaveRequest->getUser()) {
		$leaveRequest->setApproved(false);
		$this->getDoctrine()->getManager()->persist($leaveRequest);
	    }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('leave_request_index', [
                'id' => $leaveRequest->getId(),
            ]);
        }

        return $this->render('leave_request/edit.html.twig', [
            'leave_request' => $leaveRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="leave_request_delete", methods={"DELETE"})
     */
    public function delete(Request $request, LeaveRequest $leaveRequest): Response
    {
        if (!$security->getUser() == $leaveRequest->getUser()) {
        	throw $this->createNotFoundException('The page could not be found');
                // Deny access to anyone who doesnt own this leave request or is not an admin
        }
        if ($this->isCsrfTokenValid('delete'.$leaveRequest->getId(), $request->request->get('_token'))) {            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($leaveRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('leave_request_index');
    }
}
