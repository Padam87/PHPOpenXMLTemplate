<?php

namespace PHPOpenXMLTemplate;

final class Configuration
{
    /**
     * @var \PHPOpenXMLTemplate\TempDir
     */
    protected $tempDir;

    public function __construct()
    {
        $this->setTempDir();
    }

    /**
     * @param \PHPOpenXMLTemplate\TempDir $tempDir
     */
    public function setTempDir(TempDir $tempDir = null)
    {
        if ($tempDir === null) {
            $tempDir = new TempDir(sys_get_temp_dir());
        }

        $this->tempDir = $tempDir;

        return $this;
    }

    /**
     * @return \PHPOpenXMLTemplate\TempDir
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }
}