<?php

namespace PHPOpenXMLTemplate;

trait ConfigurationAwareTrait
{
    /**
     * @var \PHPOpenXMLTemplate\Configuration
     */
    protected $config;

    /**
     * @param \PHPOpenXMLTemplate\Configuration $config
     */
    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \PHPOpenXMLTemplate\Configuration $configuration
     */
    public function getConfiguration()
    {
        return $this->config;
    }
}