<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Http\Client;

use PSX\Http\Request;
use PSX\Uri\Uri;
use PSX\Uri\UriInterface;

/**
 * DeleteRequest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class DeleteRequest extends Request
{
    public function __construct(UriInterface|string $uri, array $headers = array(), mixed $body = null)
    {
        if (!$uri instanceof UriInterface) {
            $uri = new Uri((string) $uri);
        }

        parent::__construct($uri, 'DELETE', $headers, $body);

        $host = $uri->getHost();
        if (!empty($host) && !$this->hasHeader('Host')) {
            $this->setHeader('Host', $uri->getHost());
        }
    }
}
