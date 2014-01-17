<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ModelInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Listeners to ensure that we always send back JSON.

        // If we set a 404 in the ViewModel, then the RouteNotFoundStrategy will add it's own
        // fields to the ViewModel, so detect a 404 first and respond to it
        $eventManager->getSharedManager()->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($this, 'detect404'), -89);

        // Ensure we always render JSON
        $eventManager->attach(MvcEvent::EVENT_RENDER, array($this, 'ensureJsonModel'), 999);

        // Turn errors into JSON
        // $eventManager->attach(MvcEvent::EVENT_RENDER, array($this, 'renderError'));

    }

    public function detect404(MvcEvent $e)
    {
        $currentModel = $e->getResult();
        $response = $e->getResponse();

        if ($currentModel instanceof JsonModel) {
            if ($response->getStatusCode() == 404) {
                $sm = $e->getApplication()->getServiceManager();
                $renderer = $sm->get('ViewJsonRenderer');
                $response->setContent($renderer->render($currentModel));
                return $response;
            }
        }
    }

    /**
     * Ensure that any ViewModels are JsonModels
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function ensureJsonModel(MvcEvent $e)
    {
        // We ought to check for a JSON accept header here, except that we
        // don't need to as we only ever send back JSON.
        

        $currentModel = $e->getResult();
        if ($currentModel instanceof JsonModel) {
            // Current model is already a JsonModel
            return;
        }

        if ($currentModel instanceof ModelInterface) {
            $model = new JsonModel();
            $model->setTerminal(true);
            $model->setVariables($currentModel->getVariables());
            $e->setResult($model);
            $e->setViewModel($model);
        }


    }

    public function renderError(MvcEvent $e)
    {
        // must be an error
        if (!$e->isError()) {
            return;
        }

        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        // We ought to check for a JSON accept header here, except that we
        // don't need to as we only ever send back JSON.

        // If we have a JsonModel in the result, then do nothing
        $currentModel = $e->getResult();
        if ($currentModel instanceof JsonModel) {
            return;
        }

        // Create a new JsonModel and populate with default error information
        $model = new JsonModel();

        // Override with information from actual ViewModel
        $displayExceptions = true;
        if ($currentModel instanceof ModelInterface) {
            $model->setVariables($currentModel->getVariables());
            if ($model->display_exceptions) {
                $displayExceptions = (bool)$data['display_exceptions'];
            }
        }

        // Check for exception
        $exception  = $currentModel->getVariable('exception');
        if ($exception && $displayExceptions) {

            // If a code was set in the Exception, then assume that it's the
            // HTTP Status code to be sent back to the client
            if ($exception->getCode()) {
                $e->getResponse()->setStatusCode($exception->getCode());
            }


            // Should probably only render a backtrace this if in development mode!
            $model->backtrace = explode("\n", $exception->getTraceAsString());

            // Assign the message & any previous ones
            $model->message = $exception->getMessage();
            $previousMessages = array();
            while ($exception = $exception->getPrevious()) {
                $previousMessages[] = "* " . $exception->getMessage();
            };
            if (count($previousMessages)) {
                $exceptionString = implode("\n", $previousMessages);
                $model->previous_messages = $exceptionString;
            }
        }

        // Set the result and view model to our new JsonModel
        $model->setTerminal(true);
        $e->setResult($model);
        $e->setViewModel($model);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
