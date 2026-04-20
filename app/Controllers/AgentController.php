<?php

namespace App\Controllers;

use App\Repositories\AgentRepositoryInterface;
use Framework\Response;
use Framework\ResponseFactory;

class AgentController
{
    private ResponseFactory $responseFactory;

    private AgentRepositoryInterface $agentRepository;

    public function __construct(ResponseFactory $responseFactory, AgentRepositoryInterface $agentRepository)
    {
        $this->responseFactory = $responseFactory;
        $this->agentRepository = $agentRepository;
    }

    public function index(): Response
    {
        $agents = $this->agentRepository->all();
        return $this->responseFactory->view('agents/index.html.twig', [
            'agents' => $agents,
            'warrants' => []
        ]);
    }
}
