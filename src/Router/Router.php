<?php
namespace Pes\Router;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

use Pes\Action\ResourceInterface;
/**
 * Description of Router
 *
 * @author pes2704
 */
class Router implements RouterInterface, LoggerAwareInterface {

// router --> middleware : implements ContainerMiddlewareInterface
    /**
     * Objekty Route indexované podle metoy a prvího znaku pattern
     * @var Route array of
     */
    private $routes = array();

    /**
     * Objekty Route indexované podle jména routy
     * @var Route array of
     */
    private $named = array();

    /**
     * @var Route
     */
    private $matchedRoute;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    private $urlPatternValidator;

    public function __construct(UrlPatternValidator $urlPatternValidator) {
        $this->urlPatternValidator = $urlPatternValidator;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     *
     * @param ResourceInterface $resource
     * @param callable $action
     * @param type $name
     */
    public function addRoute(ResourceInterface $resource, callable $action, $name=''){
        $method = $resource->getHttpMethod();
        $urlPattern = $resource->getUrlPattern();
        $route = (new Route($this->urlPatternValidator))->setMethod($method)->setUrlPattern($urlPattern)->setAction($action);

        // přidání routy do pole rout - první index pole je metoda, druhý index pole je druhý znak url
        //  - očekávám, že druhý znak pattern bude stejný -> selže, pokud pattern začíná parametrem (první znak je dvojtečka),
        // například /:id/ (pak url je /2/ nebo /1234/ atd.) - takový pattern je nesmyslný pro REST
        // Pokud pattern (a url) je /, pak index je /
        $this->routes[$method][ $urlPattern[1] ?? '/' ][] = $route;
        if ($name) {
            $this->named[$name] = $route;
        }
    }

    /**
     * Vrací objekt Route, který byl použit při posledním routování.
     *
     * @return \Pes\Router\RouteInterface
     */
    public function getMatchedRoute(): RouteInterface {
        return $this->matchedRoute;
    }

    /**
     * Vrací request použitý při posledním routování.
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface {
        return $this->request;
    }

    /**
     * Vybere objekt Route podle metody a urlPattern routy, přidá zadanému parametru $request atribut je jménem 'route', do kterého vloží použitý objekt Route
     * pro případné využití v akci routy (například v kontroléru), vykoná action routy a vrací návratovou hodnotu vrácenou action routy.
     *
     * @param ServerRequestInterface $request
     * @return type
     * @throws RouteNotFoundException
     */
    public function route(ServerRequestInterface $request) {
        $response = $this->applyRouting($request);
        if (!$response) {
            throw new RouteNotFoundException("Route not found for method: {$request->getMethod()}, path: {$request->getUri()->getPath()}");
        }
        return $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = $this->applyRouting($request);
        if (!$response) {
            $response = $handler->handle($request);
            if (!$response) {
                throw new RouteNotFoundException("Route not found for method: {$request->getMethod()}, path: {$request->getUri()->getPath()}");
            }
        }
        return $response;
    }

    private function applyRouting(ServerRequestInterface $request) {
        $httpMethod = $request->getMethod();
//        $path = $request->getUri()->getPath();
        /** @var UriInfoInterface $uriInfo */
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME, '');
        $restUri = $uriInfo->getRestUri();
        $restUriPrefix = $restUri[1] ?? '/';
        if(array_key_exists($httpMethod, $this->routes) AND array_key_exists($restUriPrefix, $this->routes[$httpMethod])) {
            foreach($this->routes[$httpMethod][ $restUriPrefix ] as  $route) {
                $matches = array();
    //    původně:        if($httpMethod == $route->getMethod() && preg_match($route->getPattern(), $path, $matches)) {

                if(preg_match($route->getPatternPreg(), $restUri, $matches)) {
                    // odstraní první prvek $matches - $matches je pole, pro uri "/node/18856/add/" první prvek polek obsahuje "/node/18856/add/", druhý obsahuje parametr "18856"
                    // jako první prvek matches vloží $request -> první parametr předaný volané akci routy je pak $request
//                    array_shift($matches);
                    $matches[0] =$request;

                    $this->request = $request;
                    $this->matchedRoute = $route;

                    // volá route action (callable) a jako parametry volané callable předá pole $matches
                    //  vrací návratovou hodnotu action nebo FALSE v případě chyby
                    return $this->callAction($route, $matches);
                }
            }
        }
        return FALSE;
    }

    private function callAction(Route $route, $parameters) {
        $action = $route->getAction();

//        // bind container
//        $action = $this->bindToContainer($route->getAction());

        if ($this->logger) {
            $this->logBefore($route, $action, $parameters);
            $ret = call_user_func_array($action, $parameters);
            $this->logAfter($ret);
        } else {
            $ret = call_user_func_array($action, $parameters);
        }
        return $ret;
    }

    private function logBefore($route, $action, $parameters) {
        $this->logger->debug("Router: Nalezena route - method: {method}, url: {url}", ['method'=>$route->getMethod(), 'url'=>$route->getUrlPattern()]);
        $this->logger->debug("Router: Volá se {actionType} s parametry {parameters}", ['actionType'=> $this->getDebugType($action), 'parameters'=>print_r($parameters, TRUE)]);
    }

    private function logAfter($ret) {
        if($ret===FALSE) {
            $this->logger->debug("Router: Akce routy nevrátila návratovou hodnotu.");
        } else {
            $this->logger->debug("Router: Akce routy vrátila: {retType}", ['retType'=> $this->getDebugType($ret)]);
        }
    }

    private function getDebugType($var) {
        $type = gettype($var);
        $debugType = $type=='object' ? $type.': '.get_class($var) : $type;
        return $debugType;
    }
}
