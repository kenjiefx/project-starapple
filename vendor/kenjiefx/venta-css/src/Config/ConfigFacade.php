<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Config\VentaConfigInitializer;

class ConfigFacade {

  private array $argv;

  public function __construct(
    array $argv
    )
  {
    $this->argv = $argv;
  }

  public function create()
  {
    try {
      if (!isset($this->argv[2])) {
        throw new \Exception('Requires build directory', 1);
      }
    } catch (\Exception $e) {
      CoutStreamer::cout('Error: '.$e->getMessage(),'error');
    }
    VentaConfigInitializer::run($this->argv[2]);
  }

}
