<?php

namespace Pes\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Description of RedirectResponse
 *
 * @author pes2704
 */
class RedirectResponse {

    /**
     *
     * Tato třída vytvoří response se zadaným status kódem odpovídajícímm přesměrování a přidanou hlavičkou "Location:" a zadanou url.
     *
     * Pokud nezadáte status code a status code zadaného (nepřesměrovaného) requestu je 200 OK, třída doplní default hodnotu status code
     * přesměrovanému requestu: 302 Found.
     *
     * info ke status kódu:
     * The HTTP status code changes the way browsers and robots handle redirects, so if you are using header(Location:)
     * it's a good idea to set the status code at the same time.  Browsers typically re-request a 307 page every time,
     * cache a 302 page for the session, and cache a 301 page for longer, or even indefinitely.
     * Search engines typically transfer "page rank" to the new location for 301 redirects, but not for 302, 303 or 307.
     * If the status code is not specified, header('Location:') defaults to 302.
     *
     * @param ResponseInterface $response
     * @param string $url
     * @param int $status
     * @return ResponseInterface
     */
    public static function withRedirect(ResponseInterface $response, $url, $status = null) {
        $responseWithRedirect = $response->withHeader('Location', (string)$url);

        if (is_null($status) && $response->getStatusCode() === 200) {
            $status = 302;
        }

        if (!is_null($status)) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }

    /**
     * Tato metosda je vhodná pro použití při přesměrování na GET request po POST requestu při používání REST API.
     * Tato metoda vytvoří response se zadaným status kódem 303 a přidanou hlavičkou "Location:" a zadanou url.
     *
     * 303 See Other
     * The server sent this response to direct the client to get the requested resource at another URI with a GET request.
     *
     *
     * @param ResponseInterface $response
     * @param string $url
     * @return ResponseInterface
     */
    public static function withPostRedirectGet(ResponseInterface $response, $url) {
        // Excerpt from RFC-2616:
        // Note: Many pre-HTTP/1.1 user agents do not understand the 303 status. When interoperability with such clients is a concern, the 302 status code may be used instead, since most user agents react to a 302 response as described here for 303.
        $responseWithRedirect = $response->withHeader('Location', (string)$url);
        return $responseWithRedirect->withStatus(303);
    }
}
// 303 See Other
//The server sent this response to direct the client to get the requested resource at another URI with a GET request.
        // 307 a 308 pokud by POSTrequest přesměrován 307 nebo 308 prohlížeč musí použít znovu POST