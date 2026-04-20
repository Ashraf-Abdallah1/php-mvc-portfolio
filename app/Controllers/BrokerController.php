<?php

namespace App\Controllers;

use App\Models\Broker;
use App\Repositories\AgentRepositoryInterface;
use App\Repositories\BrokerRepositoryInterface;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Request;

class BrokerController
{
    private ResponseFactory $responseFactory;
    private BrokerRepositoryInterface $brokerRepository;

    private AgentRepositoryInterface $agentRepository;

    public function __construct(
        ResponseFactory $responseFactory,
        BrokerRepositoryInterface $brokerRepository,
        AgentRepositoryInterface $agentRepository
    ) {
        $this->responseFactory = $responseFactory;
        $this->brokerRepository = $brokerRepository;
        $this->agentRepository = $agentRepository;
    }

    public function index(): Response
    {
        $brokers = $this->brokerRepository->all();
        return $this->responseFactory->view('brokers/index.html.twig', [
            'brokers' => $brokers
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->get('id');
        $broker = $this->brokerRepository->find($id);
        if (!$broker) {
            return $this->responseFactory->notFound();
        }
        $agents = $this->agentRepository->brokerAgents($broker->id);
        return $this->responseFactory->view('brokers/show.html.twig', [
            'broker' => $broker,
            'agents' => $agents
        ]);
    }


    public function create(): Response
    {
        return $this->responseFactory->view('brokers/create.html.twig');
    }

    public function store(Request $request): Response
    {
        $broker = new Broker();
        $broker->name = $request->get('name') ?? '';
        $broker->id = (int)$request->get('id');
        $this->brokerRepository->insert($broker);
        return $this->responseFactory->redirect('/brokers');
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->get('id');
        $broker = $this->brokerRepository->find($id);
        if (!$broker) {
            return $this->responseFactory->notFound();
        }
        $checkedAgents = $this->agentRepository->brokerAgents($broker->id);
        $agents = $this->agentRepository->all();
        return $this->responseFactory->view('brokers/edit.html.twig', [
            "broker" => $broker,
            "checkedAgents" => $checkedAgents,
            "agents" => $agents
        ]);
    }

    public function update(Request $request): Response
    {
        $broker = new Broker();
        $broker->id = (int)$request->get('id');
        $broker->name = $request->get('name') ?? '';
        $this->brokerRepository->update($broker);

        return $this->responseFactory->redirect('/brokers/' . $broker->id);
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->get('id');
        $this->brokerRepository->delete($id);
        return $this->responseFactory->redirect('/brokers');
    }
}
