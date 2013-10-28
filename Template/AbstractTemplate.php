<?php

namespace PHPOpenXMLTemplate\Template;

use \SplFileInfo;
use \FilesystemIterator;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use PHPOpenXMLTemplate\Configuration;
use PHPOpenXMLTemplate\ConfigurationAwareTrait;

abstract class AbstractTemplate extends SplFileInfo
{
    use ConfigurationAwareTrait;

    /**
     * The extracted content
     *
     * @var \SplFileInfo
     */
    protected $content;

    /**
     * @param \PHPOpenXMLTemplate\Configuration $config
     * @param string                    $path
     */
    public function __construct(Configuration $config, $path)
    {
        $this->config = $config;

        parent::__construct(realpath($path));
    }

    public function __destruct()
    {
        if (isset($this->content)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->content->getRealPath(), FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach($iterator as $path) {
                $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
            }

            rmdir($this->content->getRealPath());
        }
    }
}