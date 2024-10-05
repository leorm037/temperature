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
use App\Form\TokenConfigurationFormType;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/token', name: 'app_token_configuration_')]
class TokenConfigurationController extends AbstractController
{
    public function __construct(
        private ConfigurationRepository $configurationRepository,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        if (null === $token) {
            $token = new Configuration();
            $token->setParamName(Configuration::CONFIGURATION_TOKEN);
            $this->configurationRepository->save($token, true);
        }

        $form = $this->createForm(TokenConfigurationFormType::class, $token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->configurationRepository->save($token, true);

            $this->addFlash('success', $this->translator->trans('message.tokenConfiguration.update.success'));

            return $this->redirectToRoute('app_token_configuration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tokenConfiguration/index.html.twig', [
            'token' => $token,
            'form' => $form,
        ]);
    }
}
