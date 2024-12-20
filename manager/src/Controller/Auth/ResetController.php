<?php

namespace App\Controller\Auth;


use App\Model\User\UseCase\Reset;
use App\Model\User\UseCase\Reset\Request\Handler;
use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/reset",name="auth.reset")
     * @param Request $request
     * @param Handler $handler
     * @return Response
     */
    public function request(Request $request, Handler $handler): Response
    {
        $command = new Reset\Request\Command();
        $form = $this->createForm(Reset\Request\Form::class, $command);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');

                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset/{token}",name="auth.reset.reset")
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @param UserFetcher $userFetcher
     * @return Response
     * @throws Exception
     */
    public function reset(string $token, Request $request, Reset\Reset\Handler $handler, UserFetcher $userFetcher): Response
    {
        if (!$userFetcher->existsByResetToken($token)){
            $this->addFlash('error','Incorrect or already confirmed token.');

            return $this->redirectToRoute('home');
        }

        $command = new Reset\Reset\Command($token);
        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');

                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}