<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\ReversionHandler;

class VentaConfigInitializer {

  public static function run(
    string $dir
    )
  {
    $ventDir = ROOT.'/vnt';
    if (!file_exists($ventDir)) {
      CoutStreamer::cout('Initializing Venta CSS');
      mkdir($ventDir);
    }
    $namespaceDir = $ventDir.'/'.$dir;
    if (!file_exists($namespaceDir)) {
      CoutStreamer::cout('Initializing /'.$dir.' namespace');
      mkdir($namespaceDir);
    }
    Self::createApp($dir);
    $reversionHandler = new ReversionHandler($dir);
    $reversionHandler->pull();
    CoutStreamer::cout('Successfully initialized /'.$dir.' namespace','success');
  }

  private static function createApp(
    string $dir
    )
  {
    $appRoot = ROOT.'/'.$dir.'/venta';
    if (!file_exists($appRoot)) {
      mkdir($appRoot);
    }
    file_put_contents($appRoot.'/app.css','');
  }

}
