<?php

namespace App\Controller;

use App\Entity\Astronaut;
use App\Form\AstronautType;
use App\Repository\AstronautRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/astronauts', name: 'astronauts')]
class AstronautController extends AbstractController
{
    #[Route('', name: '.list', methods: ['GET'])]
    public function list(AstronautRepository $astronautRepository): JsonResponse
    {
        $content = [];
        foreach ($astronautRepository->findAll() as $astronaut) {
            $content['astronauts'] = [
                'id' => $astronaut->getId(),
                'name' => $astronaut->getName(),
                'links' => [
                    'uri' => '/astronauts/' . $astronaut->getId(),
                    'rel' => 'self',
                    'method' => 'GET',
                ]
            ];
        }

        $content['links'] = [
            'uri' => '/astronauts',
            'rel' => 'create',
            'method' => 'POST'
        ];

        return $this->json($content, Response::HTTP_OK);
    }

    #[Route('', name: '.create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $astronaut = json_decode($request->getContent(), true);
        $form = $this->createForm(AstronautType::class);

        if (\json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(\json_last_error_msg(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $form->submit($astronaut);

        if (!$form->isValid()) {
            return $this->json('Invalid data submitted', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $astronaut = $form->getData();

        $entityManager->persist($astronaut);
        $entityManager->flush();

        return $this->json($this->formatAstronautResponse($astronaut), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: '.get', methods: ['GET'])]
    public function astronaut(Request $request, AstronautRepository $astronautRepository): JsonResponse
    {
        $astronaut = $astronautRepository->find($request->get('id'));

        if (!$astronaut instanceof Astronaut) {
            return $this->json('no content', Response::HTTP_NO_CONTENT);
        }

        return $this->json($this->formatAstronautResponse($astronaut),Response::HTTP_OK);
    }

    #[Pure] #[ArrayShape(['astronaut' => "array", 'links' => "\string[][]"])]
    private function formatAstronautResponse(Astronaut $astronaut): array
    {
        return [
            'astronaut' => [
                'id' => $astronaut->getId(),
                'name' => $astronaut->getName(),
                'links' => [
                    'uri' => '/astronauts/' . $astronaut->getId(),
                    'rel' => 'self',
                    'method' => 'GET',
                ],
            ],
            'links' => [
                [
                    'uri' => '/astronauts',
                    'rel' => 'create',
                    'method' => 'POST'
                ],
                [
                    'uri' => '/astronauts',
                    'rel' => 'list',
                    'method' => 'GET'
                ],
            ],
        ];
    }
}
