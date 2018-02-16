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

namespace PSX\Http\Stream;

use PSX\Http\StreamInterface;

/**
 * Buffers the complete content of the stream into an string and works from
 * there on with the buffered data
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BufferedStream extends Stream
{
    protected $source;
    protected $filled = false;

    public function __construct(StreamInterface $stream)
    {
        $this->source = $stream;
    }

    public function close()
    {
        $this->fill();

        parent::close();
    }

    public function detach()
    {
        $this->fill();

        return parent::detach();
    }

    public function getSize()
    {
        $this->fill();

        return parent::getSize();
    }

    public function tell()
    {
        $this->fill();

        return parent::tell();
    }

    public function eof()
    {
        $this->fill();

        return parent::eof();
    }

    public function rewind()
    {
        $this->fill();

        return parent::rewind();
    }

    public function isSeekable()
    {
        $this->fill();

        return parent::isSeekable();
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        $this->fill();

        return parent::seek($offset, $whence);
    }

    public function isWritable()
    {
        $this->fill();

        return parent::isWritable();
    }

    public function write($string)
    {
        $this->fill();

        return parent::write($string);
    }

    public function isReadable()
    {
        $this->fill();

        return parent::isReadable();
    }

    public function read($length)
    {
        $this->fill();

        return parent::read($length);
    }

    public function getContents()
    {
        $this->fill();

        return parent::getContents();
    }

    public function getMetadata($key = null)
    {
        $this->fill();

        return parent::getMetadata($key);
    }

    public function __toString()
    {
        $this->fill();

        return parent::__toString();
    }

    private function fill()
    {
        if ($this->filled) {
            return;
        }

        $source = $this->source->detach();
        $buffer = fopen('php://temp', 'r+');

        stream_copy_to_stream($source, $buffer, -1, 0);
        rewind($buffer);

        $this->setResource($buffer);

        $this->filled = true;
    }
}
