<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\ReversionHandler;
use \Kenjiefx\VentaCss\Build\CSSBuilder;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Build\BuildManager;

class BuilderFacade {

  private array $argv;
  private string $namespace;

  public function __construct(
    array $argv
    )
  {
    $this->argv = $argv;
  }

  public function build()
  {
      $manager = new BuildManager($this->argv);
      $manager->build();
  }

}
