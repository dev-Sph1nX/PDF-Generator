<?php

namespace App\Controller;

use App\Form\InvoiceFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        if (!$session->has('invoice_number')) {
            $session->set('invoice_number', 1);
        }


        $form = $this->createForm(InvoiceFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données soumises pour la génération du PDF
            $formData = $form->getData();

            // ... code pour la génération du PDF avec les données récupérées

            // Redirige vers une vue ou affiche directement le PDF généré
            // return $this->render('invoice_generated.html.twig', [
            //     'pdfUrl' => 'lien_vers_le_pdf',
            // ]);

            // Pour l'exemple, redirige vers une vue de confirmation
            return $this->render('invoice_confirmation.html.twig', [
                'formData' => $formData,
            ]);
        }

        return $this->render('invoice_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
