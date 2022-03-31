<?php

namespace Kenjiefx\VentaCss\Venta;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Config\VentaConfigInitializer as Config;


class Venta {

    public string $namespace;
    private string $frontend;
    private string $backend;
    private array $config;

    public function __construct(
        string|null $namespace = null
        )
    {
        $this->config = Config::load();
        $this->namespace = $namespace ?? $this->config['namespace'];
        $this->frontend  = ROOT.'/'.$this->namespace;
        $this->backend   = ROOT.'/vnt/'.$this->namespace;
        $this->validate();
    }

    private function validate()
    {
        try {
            if (!file_exists($this->frontend)) {
                throw new \Exception(
                    'Build directory /'.$this->namespace.' not found'
                );
            }
            if (!file_exists($this->backend)) {
                throw new \Exception(
                    'Build directory /'.$this->namespace.' not found'
                );
            }
        } catch (\Exception $e) {
            CoutStreamer::cout($e->getMessage(),'error');
            exit();
        }
    }

    /**
     * @throws Exception Unable to find build CSS
     * When checking to see if the venta constant
     * path $root/namespace/venta/app.css
     * do not exist
     *
     * @return string - A CSS file content
     */
    public function getCssToBuild(): string
    {
        $appCssPath = $this->frontend.'/venta/app.css';
        try {
            if (!file_exists($appCssPath)) {
                throw new \Exception(
                    'Unable to load Venta raw CSS: /venta/app.css'
                );
            }
        } catch (\Exception $e) {
            CoutStreamer::cout($e->getMessage(),'error');
            exit();
        }
        return file_get_contents($appCssPath);
    }

    public function getFrontend(): string
    {
        return $this->frontend;
    }

    public function getBackend(): string
    {
        return $this->backend;
    }

}
