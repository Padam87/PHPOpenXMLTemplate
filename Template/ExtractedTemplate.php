<?php

namespace PHPOpenXMLTemplate\Template;

use \SplFileInfo;
use \FilesystemIterator;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use PHPOpenXMLTemplate\Configuration;

/**
 * Extracted OpenXML document template
 */
class ExtractedTemplate extends AbstractTemplate implements TemplateInterface
{
    /**
     * @var string The format / extension of the file
     */
    protected $format;

    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $config, $path, $format)
    {
        $this->format = $format;

        parent::__construct($config, $path);

        if (!$this->isDir()) {
            throw new FileException(sprintf('"%" is not a valid extracted OpenXML file.', $path));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (!isset($this->content)) {
            $destination = $this->config->getTempDir()->getPathname() . DIRECTORY_SEPARATOR . $this->getFilename() . uniqid();

            mkdir($destination);

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->getRealPath(), FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach($iterator as $path) {
                $relpath =  str_replace($this->getRealPath() . DIRECTORY_SEPARATOR, "", $path->getRealPath());

                $path->isFile()
                        ? copy($path->getRealPath(), $destination . DIRECTORY_SEPARATOR . $relpath)
                        : mkdir($destination . DIRECTORY_SEPARATOR . $relpath);
            }

            $content = new SplFileInfo($destination);

            $this->content = $content;
        }

        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return parent::getBasename() . "." . $this->format;
    }
}