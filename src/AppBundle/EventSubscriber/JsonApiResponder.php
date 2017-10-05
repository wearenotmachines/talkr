<?php
namespace AppBundle\EventSubscriber;

use AppBundle\Controller\JsonApiController;
use AppBundle\Exceptions\InvalidJsonRequestException;
use AppBundle\Validators\JsonInputValidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonApiResponder implements EventSubscriberInterface
{

    private $data;
    private $requestValidator;

    public function __construct()
    {

        $this->requestValidator = new JsonInputValidator();

    }

    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof JsonApiController) {

            //validate the content of the request as json - if it has content
            $request = $event->getRequest();

            $requestBody = $request->getContent();
            $this->requestValidator->setInput($requestBody);

            if (!$this->requestValidator->validate()) {
                throw new InvalidJsonRequestException($this->requestValidator->getErrorString(), $this->requestValidator->getErrorCode());
            }

        }

    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        $exception = $event->getException();

        $jsonResponse = new JsonResponse();

        if ($exception instanceof InvalidJsonRequestException) {
            $jsonResponse->setStatusCode($exception->getHttpStatusCode());
        } else {
            throw $exception;
        }

        $output = [
            "errors" => [$exception->getMessage()],
            "error_code" => $exception->getCode(),
        ];

        $jsonResponse->setData($output);

        $event->setResponse($jsonResponse);

    }

    public static function getSubscribedEvents()
    {

        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::VIEW => 'onKernelView',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {

        $jsonResponse = new JsonResponse();

        $jsonResponse->setData($this->wrapApiResponse($event->getRequest(), $event->getControllerResult()));

        $event->setResponse($jsonResponse);
    }

    private function wrapApiResponse(Request $request, array $data)
    {

        return [
            "api_version" => 1.0,
            "url" => $request->getPathInfo(),
            "method" => $request->getMethod(),
            "t" => (new \DateTime)->format("c"),
            "data" => $data,
        ];

    }

}
