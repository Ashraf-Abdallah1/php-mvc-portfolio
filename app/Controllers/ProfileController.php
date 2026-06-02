<?php

namespace App\Controllers;

use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProfileController
{
    private ResponseFactory $responseFactory;
    private ProfileRepositoryInterface $profileRepository;
    private AuthService $auth;

    public function __construct(
        ResponseFactory $responseFactory,
        ProfileRepositoryInterface $profileRepository,
        AuthService $auth
    ) {
        $this->responseFactory = $responseFactory;
        $this->profileRepository = $profileRepository;
        $this->auth = $auth;
    }

    public function index(): Response
    {
        $isAdmin = $this->auth->isAdmin();

        $profile = $this->profileRepository->findByUserId(1);

        return $this->responseFactory->view('profile/index.html.twig', [
            'profile' => $profile,
            'isAdmin' => $isAdmin
        ]);
    }

    public function edit(): Response
    {
        $userId = $this->auth->getUserId();
        $profile = $this->profileRepository->findById(1);

        return $this->responseFactory->view('profile/edit.html.twig', [
            'profile' => $profile
        ]);
    }

    public function update(Request $request): Response
    {
        $profile = new Profile();
        $profile->setName($request->get('name'));
        $profile->setBio($request->get('bio'));
        $profile->setSkills($request->get('skills'));
        $profile->setExperience($request->get('experience'));
        $profile->setProjects($request->get('projects'));
        $profile->setContact($request->get('contact'));

        $this->profileRepository->update($profile);

        return $this->responseFactory->redirect('/profile');
    }
}
