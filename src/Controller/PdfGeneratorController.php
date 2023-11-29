<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class PdfGeneratorController extends AbstractController
{
    public function generatePDF(Request $request): Response
    {
        $session = $request->getSession();

        $invoiceNumber = $session->get('invoice_number');
        $session->set('invoice_number', $invoiceNumber + 1);

        $generatedInvoiceNumber = 'INV-' . str_pad($invoiceNumber, 3, '0', STR_PAD_LEFT);

        $clientName = $request->get('clientName');
        $products = $request->get('products');

        $total = array_reduce($products, function ($carry, $item) {
            return $carry + $item['quantity'] * $item['price'];
        }, 0);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Crée une nouvelle page
        $pdf->AddPage();

        // Récupère le contenu HTML du fichier facture.html.twig
        $html = $this->renderView('facture.html.twig', [
            'invoiceNumber' => $generatedInvoiceNumber,
            'clientName' => $clientName,
            'items' => $products,
            'total' => $total,
        ]);

        // Écrit le contenu HTML dans le PDF
        $pdf->writeHTML($html);

        // Nom du fichier PDF de sortie
        $fileName = 'facture_' . date('Y-m-d') . '.pdf';

        // Renvoie le PDF en tant que réponse HTTP
        return new Response($pdf->Output($fileName, 'D'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    // #[Route('/pdf/generator', name: 'app_pdf_generator')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/PdfGeneratorController.php',
    //     ]);
    // }
}
