<?php

namespace PHPOpenXMLTemplate\Template;

interface TemplateInterface
{
    /**
     * @returns \SplFileInfo
     */
    function getContent();

    /**
     * Gets the filename
     *
	 * @return string The filename.
     */
    function getFilename();

    /**
     * Gets the pathname
     *
	 * @return string The pathname.
     */
    function getPathname();
}