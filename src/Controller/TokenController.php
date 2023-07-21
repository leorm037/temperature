<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Form\TokenConfigurationFormType;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TokenController extends AbstractController
{

    private ConfigurationRepository $configurationRepository;
    private TranslatorInterface $translator;

    public function __construct(
            ConfigurationRepository $configurationRepository,
            TranslatorInterface $translator
    ){
        $this->configurationRepository = $configurationRepository;
        $this->translator = $translator;
    }

    public function index(Request $request): Response
    {
        $token = $this->configurationRepository->findByName('token');

        if (null === $token) {
            $token = new Configuration();
            $token->setParamName('token');
            $token->setParamValue('token');
            $this->configurationRepository->save($token, true);
        }

        $form = $this->createForm(TokenConfigurationFormType::class, $token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->configurationRepository->save($token, true);

            $this->addFlash('success', $this->translator->trans('message.token.update.success'));

            return $this->redirectToRoute('app_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('token/index.html.twig', [
                    'token' => $token,
                    'form' => $form,
        ]);
    }
}
