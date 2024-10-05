<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Configuration;
use App\Form\ConfigurationType;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/configuration', name: 'app_configuration')]
class ConfigurationController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ConfigurationRepository $configurationRepository): Response
    {
        return $this->render('configuration/index.html.twig', [
            'configurations' => $configurationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $configuration = new Configuration();
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($configuration);
            $entityManager->flush();

            return $this->redirectToRoute('app_configuration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('configuration/new.html.twig', [
            'configuration' => $configuration,
            'form' => $form,
        ]);
    }

    #[Route('/{id:configuration}', name: 'show', methods: ['GET'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function show(Configuration $configuration): Response
    {
        return $this->render('configuration/show.html.twig', [
            'configuration' => $configuration,
        ]);
    }

    #[Route('/{id:configuration}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function edit(Request $request, Configuration $configuration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_configuration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('configuration/edit.html.twig', [
            'configuration' => $configuration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function delete(Request $request, Configuration $configuration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$configuration->getId(), $request->request->get('_token'))) {
            $entityManager->remove($configuration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_configuration_index', [], Response::HTTP_SEE_OTHER);
    }
}
