<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Http\Parser;

use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;
use PSX\Uri\UriResolver;
use PSX\Uri\Url;

/**
 * RequestParser
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestParser extends ParserAbstract
{
    private ?Url $baseUrl;

    public function __construct(?Url $baseUrl = null, int $mode = self::MODE_STRICT)
    {
        parent::__construct($mode);

        $this->baseUrl = $baseUrl;
    }

    /**
     * Converts an raw http request into an PSX\Http\Request object
     *
     * @throws ParseException
     */
    public function parse(string $content): Request
    {
        $content = $this->normalize($content);

        list($method, $path, $scheme) = $this->getStatus($content);

        // resolve uri path
        if ($this->baseUrl !== null) {
            $path = UriResolver::resolve($this->baseUrl, new Uri($path));
        } else {
            $path = new Uri($path);
        }

        $request = new Request($path, $method);
        $request->setProtocolVersion($scheme);

        list($header, $body) = $this->splitMessage($content);

        $this->headerToArray($request, $header);

        $request->setBody(new StringStream($body));

        return $request;
    }

    /**
     * @throws ParseException
     */
    protected function getStatus(string $request): array
    {
        $line = $this->getStatusLine($request);

        if ($line !== false) {
            $parts = explode(' ', $line, 3);

            if (isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
                $method = $parts[0];
                $path   = $parts[1];
                $scheme = $parts[2];

                return array($method, $path, $scheme);
            } else {
                throw new ParseException('Invalid status line format');
            }
        } else {
            throw new ParseException('Couldnt find status line');
        }
    }

    public static function buildStatusLine(RequestInterface $request): string
    {
        $method   = $request->getMethod();
        $target   = $request->getRequestTarget();
        $protocol = $request->getProtocolVersion();

        if (empty($target)) {
            throw new \RuntimeException('Target not set');
        }

        $method   = !empty($method) ? $method : 'GET';
        $protocol = !empty($protocol) ? $protocol : 'HTTP/1.1';

        return $method . ' ' . $target . ' ' . $protocol;
    }

    /**
     * Parses an raw http request into an PSX\Http\Request object. Throws an
     * exception if the request has not an valid format
     *
     * @throws ParseException
     */
    public static function convert(string $content, Url $baseUrl = null, int $mode = ParserAbstract::MODE_STRICT): RequestInterface
    {
        $parser = new self($baseUrl, $mode);

        return $parser->parse($content);
    }
}
