<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\UserService\UserRepository;

class GeneratePdf
{
    protected UserRepository $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        // return response($pdf->stream('report.pdf'), 200)
        //     ->header('Content-Type', 'application/pdf');

    }

    public function generatePdf($data, $productTypeName)
    {
        $branchData = $this->userRepository->getuserOrgAndBranchDetail();



        // Pass $branchData, $data, and $productTypeName to the view
        $pdf = Pdf::loadView('reportPdf', compact('branchData', 'data', 'productTypeName'));

        return $pdf->download('report.pdf');
    }


}
