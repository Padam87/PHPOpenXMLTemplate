<?php

namespace PHPOpenXMLTemplate;

use \SplFileInfo;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

/**
 * Temp directory
 */
class TempDir extends SplFileInfo
{
    public function __construct($path)
    {
        parent::__construct(realpath($path));

        if (!$this->isDir()) {
            throw new UnexpectedTypeException(sprintf('"%" is not a directory.', $path));
        }

        if (!$this->isWritable()) {
            throw new UnexpectedTypeException(sprintf('"%" is not writable.', $path));
        }
    }
}