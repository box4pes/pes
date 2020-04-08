<?php

/**
 * Description of Dispatcher
 *
 * @author Rasmus Schultz mindplay-dk https://github.com/mindplay-dk/middleman, úprava pes2704
 * Úprava na poslední verzi Psr\Http\Server - MiddlewareInterface a RequestHandlerInterface + vrací Pes\Http\ResponseInterface
 */

namespace Pes\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppInterface;
use Pes\Middleware\AppMiddlewareInterface;

/**
 * PSR-7 / PSR-15 middleware dispatcher
 */
class Dispatcher implements MiddlewareInterface
{
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var callable middleware resolver
     */
    private $resolver;

    /**
     * @var mixed[] unresolved middleware stack
     */
    private $stack;

    /**
     * Přijímá zásobník middleware a případně resolver. Položky zásobníku middleware jsou jednotlivá middleware nebo callable nebo, v případě zadaného resolveru,
     * libovolné hodnoty. Resolver je anonymní funkce, která je volána při vyhodnocování zásobníku, položka zásobníku je použita jako argument
     * resolveru a resolver vrací middleware nebo callable, které je následně spuštěno. Obecně je parametr resolveru a tedy položka zásobníkulibovolná hodnata,
     * ale
     *
     * @param (callable|MiddlewareInterface|mixed)[] $stack Zásobník middleware (s alespoň jedním prvvkem) ve formě pole, zásobník je vyhodnocován od nejnižšího indexu
     * @param callable|null $resolver optional middleware resolver:
     *                                function (string $name): MiddlewareInterface
     * @param AppInterface $app
     *
     * @throws InvalidArgumentException if an empty middleware stack was given
     */
    public function __construct($stack, callable $resolver = null, AppInterface $app=NULL)
    {
        if (count($stack) === 0) {
            throw new \InvalidArgumentException("Zadáno prázdné pole middleware do zásobníku middleware.");
        }
        $this->stack = $stack;
        $this->resolver = $resolver;
        $this->app = $app;
    }

    /**
     * Vykoná všechny middleware zařazené do zásobníku middleware předaného do konstruktoru dispatcheru.
     * Vykonávání končí jakmile některá položka middleware v zásobníku vrátí response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws LogicException on unexpected result from any middleware on the stack
     */
//    public function dispatch(ServerRequestInterface $request)
//    {
//        $resolved = $this->resolve(0);
//        return $resolved($request);
//    }

    /**
     * {@inheritdoc}
     *
     * Impementace přijímá PSR request a vrací PSR response.
     * Vykoná všechny middleware zařazené do zásobníku middleware předaného do konstruktoru dispatcheru.
     * Vykonávání končí jakmile některá položka middleware v zásobníku vrátí response.
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
//        $this->stack[] = function (ServerRequestInterface $request) use ($handler) {
//            return $handler->handle($request);
//        };
        $this->stack[] = $handler;
//        $response = $this->dispatch($request);
        $resolved = $this->resolve(0);
        return $resolved($request);
//        array_pop($this->stack);
//        return $response;
    }

    /**
     * Vytvoří request handler. Tento request handler po zavolání vytváří response rekurzivním projitím zásobníku middleware předaného do konstruktoru dispatcheru.
     * <p>Request handler REKURZIVNÉ projde celý zásobník takto:
     * <ul>
     * <li>pokud je zadán resolver položku zásobníku nejprve resolvuje, resolver je funkce, která příjímá parametr a vrací
     * middleware - objekt typu Psr\Http\Server\MiddlewareInterface.
     * <li>Pro výsledek vrácený resolverem nebo položku přímo: pokud je výsledek nebo položka Middleware - volá metodu položka->process(), jinak vyhodí výjimku</li>
     *    Jako druhý parament při voláníí middleware předá rekurzivní volání resolve() s další položkou zásobníku.</li>
     * <li>Pokud volání položky zásobníku vrací response, rekurze končí.</li></ul>
     * </p>
     * @param int $index middleware stack index
     *
     * @return RequestHandlerInterface
     */
    private function resolve($index) {
        if (isset($this->stack[$index])) {
            if ($this->stack[$index] instanceof RequestHandlerInterface) {
                return $this->stack[$index];
            } else {
                return new RequestHandler(
                    function (ServerRequestInterface $request) use ($index) {
                        //zavolá na položku resolver (výsledek musí být middleware nebo callable) nebo použije položku
                        $middleware = $this->resolver ? call_user_func($this->resolver, $this->stack[$index]) : $this->stack[$index];
                        if ($middleware instanceof MiddlewareInterface) {
                            if ($middleware instanceof AppMiddlewareInterface) {
                                $middleware->setApp($this->app);
                            }
                            $result = $middleware->process($request, $this->resolve($index + 1));
                            if (! $result instanceof ResponseInterface) {
                                throw new \DomainException("Middleware v zásobníku s indexem $index nevrátilo objekt typu ResponseInterface.");
                            }
                        } else {
                            throw new \UnexpectedValueException("Nepodporovaný typ middleware ".get_class($this->stack[$index])." v zásobníku s indexem $index. Podporované jsou objekty typu MiddlewareInterface.");
                        }
                        return $result;
                    }
                );
            }
        }
        return new UnprocessedRequestHandler();
    }
}
