<?php
namespace Pes\Router;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Router\Exception\RouteNotFoundException;

use Pes\Router\RouteInterface;
use Pes\Http\Helper\UriInfoInterface;
use Pes\Http\Request;

/**
 * Description of Router
 *
 * @author pes2704
 */
class Router implements RouterInterface, LoggerAwareInterface {

// router --> middleware : implements ContainerMiddlewareInterface
    /**
     * Objekty Route indexované podle metody a prvího znaku pattern
     * @var array<string, array<string, RouteInterface[]>> array of array of RouteInterface
     */
    private $routes = [];

    /**
     * @var RouteInterface
     */
    private $matchedRoute;

    /**
     * @var array
     */
    private $matches = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ServerRequestInterface
     */
    private $matchedRequest;

    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

    /**
     *
     * @param RouteInterface $route
     */
    public function addRoute(RouteInterface $route): void {
        // přidání routy do pole rout - první index pole je metoda, druhý index pole je druhý znak url
        //  - očekávám, že druhý znak pattern bude stejný -> selže, pokud pattern začíná parametrem (první znak je dvojtečka),
        // například /:id/ (pak url je /2/ nebo /1234/ atd.) - takový pattern je nesmyslný pro REST
        // Pokud pattern (a url) je /, pak index je /
        $resource = $route->getResource();
        $this->routes[$resource->getHttpMethod()][ $resource->getUrlPattern()[1] ?? '/' ][] = $route;
    }

    public function exchangeRoutes(\Traversable $routes): void {
        $this->routes = [];
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * Vrací objekt Route, který byl použit při posledním routování.
     *
     * @return RouteInterface
     */
    public function getMatchedRoute(): RouteInterface {
        return $this->matchedRoute;
    }

    /**
     * Vrací request použitý při posledním routování.
     * @return ServerRequestInterface
     */
    public function getMatchedRequest(): ServerRequestInterface {
        return $this->matchedRequest;
    }

    /**
     * Vybere objekt Route podle http metody a urlPattern rout. Pokud nalezne odpovídající route, vykoná action routy a vrací návratovou hodnotu
     * vrácenou action routy. Pokud nenalezne odpovídající route, vyhodí výjimku RouteNotFoundException.
     *
     * Pokud nalezne odpovídající route:
     * - Předanému parametru metody - objektu ServerRequestInterface přidá atribut je jménem 'route', do kterého vloží použitý objekt Route
     * pro případné využití v akci routy (například v kontroléru)-
     * - Objektu Router nastaví potřebné hodnoty request a route pro případné volání metod getMatchedRoute() a getMatchedRequest() routeru po routování.
     *
     * @param ServerRequestInterface $request
     * @return type
     * @throws RouteNotFoundException
     */
    public function route(ServerRequestInterface $request): ResponseInterface {
        if ($this->findRoute($request)) {
            return $this->callMatchedRouteAction();
        } else {
            throw new RouteNotFoundException("Route not found for method: {$request->getMethod()}, path: {$request->getUri()->getPath()}");
        }
    }

    /**
     * Implmentuje Middleware interface. Tato implementace nejprve provede routovánmí a jen v případě nenalezení routy volá request handler.
     *
     * Vybere objekt Route podle http metody a urlPattern rout. Pokud nalezne odpovídající route, vykoná action routy. Pokud akce routy vrátila response,
     * vrací tento response - návratovou hodnotu vrácenou action routy. Pokud nenalezne odpovídající route nebo akce routy nevrátila response,
     * volá request handler - předá request ke zpracování další vrstvě middleware.
     *
     * Pokud nalezne odpovídající route:
     * - Předanému parametru metody - objektu ServerRequestInterface přidá atribut je jménem 'route', do kterého vloží použitý objekt Route
     * pro případné využití v akci routy (například v kontroléru)-
     * - Objektu Router nastaví potřebné hodnoty request a route pro případné volání metod getMatchedRoute() a getMatchedRequest() routeru po routování.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if ($this->findRoute($request)) {
            return $this->callMatchedRouteAction();
        } else {
            return $handler->handle($request);
        }
    }

    private function findRoute(ServerRequestInterface $request) {
        $httpMethod = $request->getMethod();
        /** @var UriInfoInterface $uriInfo */
        $uriInfo = $request->getAttribute(Request::URI_INFO_ATTRIBUTE_NAME);   // pokud bylo UriInfo v AppFactory
        if($uriInfo instanceof UriInfoInterface) {
            $restUri = $uriInfo->getRestUri();
        } else {
            $restUri = $this->getRoutingPath($request);
        }
        $restUriPrefix = $restUri[1] ?? '/';
        if(array_key_exists($httpMethod, $this->routes) AND array_key_exists($restUriPrefix, $this->routes[$httpMethod])) {
            foreach($this->routes[$httpMethod][ $restUriPrefix ] as  $route) {
                $matches = array();
                // původně: if($httpMethod == $route->getMethod() && preg_match($route->getPattern(), $path, $matches)) {
                if(preg_match($route->getPatternPreg(), $restUri, $matches)) {
                    $this->logger?->debug("Router: restUri $restUri => route - method: {method}, urlPattern: {url}", ['method'=>$route->getResource()->getHttpMethod(), 'url'=>$route->getResource()->getUrlPattern()]);
                    $this->matchedRoute = $route;
                    $this->matches = $matches;
                    $this->matchedRequest = $request->withAttribute('route', $route);
                    return true;
                }
            }
        }
        return false;
    }

    private function getRoutingPath(ServerRequestInterface $request): string {
        
        // $uri = $request->getUri()->getPath();
        // $serverParams = $request->getServerParams();

        // // 1. Získáme název aktuálního skriptu (např. "/public/index.php" nebo "/app.php")
        // $scriptName = $serverParams['SCRIPT_NAME'] ?? '';

        // // 2. Bezpečně získáme pouze název souboru (např. "index.php" nebo "app.php")
        // $scriptFilename = basename($scriptName);

        // // 3. Získáme základní adresář (basePath) bez názvu souboru a koncových lomítek
        // $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        // // 4. Pokud URI začíná touto basePath, ořízneme ji
        // if (!empty($basePath) && strpos($uri, $basePath) === 0) {
        //     $uri = substr($uri, strlen($basePath));
        // }

        // // 5. Odstraníme úvodní lomítko pro snazší porovnání názvu souboru
        // $uri = ltrim($uri, '/');

        // // 6. Dynamické ošetření přepisu: Pokud zbylé URI odpovídá názvu spuštěného souboru
        // if ($uri === $scriptFilename) {
        //     $uri = '';
        // } elseif (strpos($uri, $scriptFilename . '/') === 0) {
        //     $uri = substr($uri, strlen($scriptFilename) + 1);
        // }

        // // 7. Vždy vrátíme cestu začínající lomítkem
        // return '/' . ltrim($uri, '/');        

        // subdomain path a rest uri
        $requestScriptName = parse_url($request->getServerParams()['SCRIPT_NAME'], PHP_URL_PATH);
        $requestScriptDir = dirname($requestScriptName);

        $requestUri = rawurldecode($request->getUri()->getPath());  // metoda getPath musí vracet enkódované uri 

        if (stripos($requestUri, $requestScriptName) === 0) {
            $subDomainPath = $requestScriptName;
        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
            $subDomainPath = $requestScriptDir;
        } else {
            $subDomainPath = '';
        }

        if ($subDomainPath) {
            $virtualPath = substr($requestUri, strlen($subDomainPath));
        } else {
            $virtualPath = $requestUri;
        }

        return'/'.ltrim($virtualPath, '/');

    }

    private function callMatchedRouteAction(): ResponseInterface {

        $action = $this->matchedRoute->getAction();

//        // bind container
//        $action = $this->bindToContainer($route->getAction());

        $this->logBefore($action);

        // jako první prvek pole matches vloží $request -> první parametr předaný volané action callable routy je pak $request
        // užití v callable: function(ServerRequestInterface $request, $uid) { ... }
        $this->matches[0] = $this->matchedRequest;
        $ret = call_user_func_array($action, $this->matches);

        $this->logAfter($ret);
        return $ret;
    }

    private function logBefore($action) {
        $this->logger?->debug("Router: Volá se {actionType} s parametry {parameters}", ['actionType'=> $this->getDebugType($action), 'parameters'=> implode(', ', $this->matches)]);
    }

    private function logAfter($ret) {
        if($ret===FALSE) {
            $this->logger?->warning("Router: Akce routy nevrátila návratovou hodnotu.");
        } elseif(! $ret instanceof ResponseInterface) {
            $this->logger?->debug("Router: Akce routy nevrátile Response, vrátila: {retType}", ['retType'=> $this->getDebugType($ret)]);
        } else {
            $this->logger?->notice("Router: Akce routy vrátila: {retType}, {status}, {reasonPhrase}",
                    [
                        'retType'=> $this->getDebugType($ret),
                        'status'=>$ret->getStatusCode(),
                        'reasonPhrase'=>$ret->getReasonPhrase()
                    ]);
        }
    }

    private function getDebugType($var) {
        $type = gettype($var);
        $debugType = $type=='object' ? $type.': '.get_class($var) : $type;
        return $debugType;
    }
}
