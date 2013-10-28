<?php

namespace PHPOpenXMLTemplate;

use \SplFileInfo;
use \ZipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \Twig_Environment;
use \Twig_Loader_Filesystem;
use Symfony\Component\Finder\Finder;
use PHPOpenXMLTemplate\Template\TemplateInterface;

/**
 * Template parser
 */
class Parser
{
    use ConfigurationAwareTrait;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @param \PHPOpenXMLTemplate\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param \PHPOpenXMLTemplate\TemplateInterface $template
     * @param array $parameters
     */
    public function parse(TemplateInterface $template, $parameters = array())
    {
        $content = $template->getContent();

        $loader = new Twig_Loader_Filesystem();
        $loader->addPath($content->getPathName());
        $this->twig = new Twig_Environment($loader);
        $this->finder = new Finder();

        $files = $this->finder->files()->name('*.xml')->in($content->getPathname());

        foreach ($files as $file) {
            $relPath = str_replace($content->getPathname() . DIRECTORY_SEPARATOR, '', $file->getPathName());
            $rendered = $this->twig->render($relPath, $parameters);

            $fh = $file->openFile("w");
            $fh->ftruncate(0);
            $fh->fwrite($rendered);
        }

        return $this->create($content, $template);
    }

    /**
     * @param \SplFileInfo $temp
     * @param \PHPOpenXMLTemplate\TemplateInterface $template
     *
     * @return \SplFileInfo
     */
    protected function create(SplFileInfo $content, TemplateInterface $template)
    {
        $zip = new ZipArchive();

        $path = new SplFileInfo($this->config->getTempDir()->getPathname() . DIRECTORY_SEPARATOR . uniqid() . $template->getFilename());

        $zip->open($path->getPathname(), ZipArchive::CREATE);

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($content->getPathName()));

        foreach ($files as $file) {
            if (in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1), array('.', '..'))) {
                continue;
            }

            $name = str_replace($content->getBasename() . DIRECTORY_SEPARATOR, '', $file);
            $name = str_replace($this->config->getTempDir()->getRealPath() . DIRECTORY_SEPARATOR, '', $name);

            if (is_dir($file) === true) {
                $zip->addEmptyDir($name . DIRECTORY_SEPARATOR);
            } elseif (is_file($file) === true) {
                $zip->addFromString($name, file_get_contents($file));
            }
        }

        $zip->close();

        return $path;
    }
}