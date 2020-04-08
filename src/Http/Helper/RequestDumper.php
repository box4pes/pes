<?php
namespace Pes\Http\Helper;

use Psr\Http\Message\RequestInterface;
use Pes\Text\Template;

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Description of RequestDumper
 *
 * @author pes2704
 */
class RequestDumper {
    /**
     * Vyoíše sadu informací o requestu veformě pole.
     * @param RequestInterface $request
     * @return string
     */
    public static function dump(RequestInterface $request) {
        $dump[] = Template::interpolate('REQUEST DUMP - '.$request->getMethod().' request');
        $dump[] = Template::interpolate('URI Scheme: {Scheme}', ['Scheme'=>$request->getUri()->getScheme()]);
        $dump[] = Template::interpolate('URI Authority: {Authority}', ['Authority'=>$request->getUri()->getAuthority()]);
        $dump[] = Template::interpolate('URI Path: {Path}', ['Path'=>$request->getUri()->getPath()]);
        $dump[] = Template::interpolate('URI Query: {Query}', ['Query'=>$request->getUri()->getQuery()]);
        $dump[] = Template::interpolate('URI Fragment: {Fragment}', ['Fragment'=>$request->getUri()->getFragment()]);

        $dump[] = Template::interpolate('Headers: {Headers}', ['Headers'=>print_r($request->getHeaders(), TRUE)]);
        $dump[] = Template::interpolate('Cookies: {CookieParams}', ['CookieParams'=>print_r($request->getCookieParams(), TRUE)]);
        $dump[] = Template::interpolate('Attributes: {Attributes}', ['Attributes'=>print_r($request->getAttributes(), TRUE)]);
        $dump[] = Template::interpolate('Body: {Body}Body size: {size}.', ['Body'=>print_r($request->getBody(), TRUE), 'size'=>$request->getBody()->getSize()]);
        $dump[] = Template::interpolate('POST parsed body: {ParsedBody}', ['ParsedBody'=>print_r($request->getParsedBody(), TRUE)]);
        $dump[] = Template::interpolate('Query params: {QueryParams}', ['QueryParams'=>print_r($request->getQueryParams(), TRUE)]);
        $dump[] = Template::interpolate('Request target: {RequestTarget}', ['RequestTarget'=>print_r($request->getRequestTarget(), TRUE)]);
        $dump[] = Template::interpolate('Server params: {ServerParams}', ['ServerParams'=>print_r($request->getServerParams(), TRUE)]);
        $dump[] = Template::interpolate('Uploaded files: {UploadedFiles}', ['UploadedFiles'=>print_r($request->getUploadedFiles(), TRUE)]);

        return implode(PHP_EOL, $dump);
    }

}
