<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionListener
{
    private $router;
    private $requestStack;

    //constructor with dependency injection SessionInterface $session
    public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );



        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if($exception instanceof ForeignKeyConstraintViolationException) {

            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Kan niet verwijderd worden');

            $redirectRoute = $this->requestStack->getCurrentRequest()->headers->get('referer');

            $response->

            $event->setResponse(new RedirectResponse($redirectRoute));
        }


        // sends the modified response object to the event
        $event->setResponse($response);
    }
}