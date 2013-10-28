<?php

namespace PHPOpenXMLTemplate\Template;

use \SplFileInfo;
use \ZipArchive;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use PHPOpenXMLTemplate\Configuration;

/**
 * OpenXML document template
 */
class Template extends AbstractTemplate implements TemplateInterface
{
    public static $MIME_TYPES = array(
        "docm" => "application/vnd.ms-word.document.macroEnabled.12",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "dotm" => "application/vnd.ms-word.template.macroEnabled.12",
        "dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
        "ppsm" => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
        "ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
        "pptm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
        "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
        "xlsm" => "application/vnd.ms-excel.sheet.macroEnabled.12",
        "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "xps"  => "application/vnd.ms-xpsdocument",
    );

    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $config, $path)
    {
        parent::__construct($config, $path);

        if (!$this->isFile()) {
            throw new FileNotFoundException($path);
        }

        if (!in_array($this->getExtension(), array_keys(self::$MIME_TYPES))) {
            if (!in_array($this->getMimeType(), array_values(self::$MIME_TYPES))) {
                throw new FileException(sprintf('"%" is not a valid OpenXML file.', $path));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (!isset($this->content)) {
            $zip = new ZipArchive();

            $result = $zip->open($this->getPathname());

            if ($result !== true) {
                throw new FileException(sprintf('"%" is not a valid zip archive.', $this->getPathname()));
            }

            $content = new SplFileInfo($this->config->getTempDir()->getPathname() . DIRECTORY_SEPARATOR . $this->getBasename() . uniqid());

            if (!$content->isDir()) {
                mkdir($content->getPathname());
            }

            $zip->extractTo($content->getPathname());
            $zip->close();

            $this->content = $content;
        }

        return $this->content;
    }
}