<?php

namespace App\Controllers;

use Framework\Response;
use Framework\ResponseFactory;

class faqController
{

    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(): Response
    {
        return $this->responseFactory->view('faq/index.html.twig', [
            'active' => "faq"
        ]);
    }

}