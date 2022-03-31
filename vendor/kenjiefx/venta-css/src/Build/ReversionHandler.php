<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Venta\Venta;

class ReversionHandler {

    private array $argv;
    private Venta $venta;
    private string $namespace;

    public function __construct(
        array $argv
    )
    {
        $this->argv = $argv;
        $this->venta = new Venta();
    }

        public function pull(
            $dirName = null
            )
        {
            $dir = $dirName ?? '/';
            try {
                if (!file_exists($this->venta->getFrontend().$dir)) {
                    throw new \Exception('Build directory /'.$this->venta->namespace.' not found');
                }
            } catch (\Exception $e) {
                CoutStreamer::cout('Error: '.$e->getMessage(),'error');
                exit();
            }

            $pullDir = $this->venta->getBackend().$dir;

            FileSys::clear($pullDir);

            FileSys::traverse($this->venta->getFrontend().$dir,function(
                $filePath,
                $fileName,
                $fileExtension,
                $closureArgs
            ){
                if ($fileExtension==='dir') {
                    $puller = new ReversionHandler($this->argv);
                    $puller->pull('/'.$fileName);
                    return;
                }
                if (!file_exists($closureArgs['pullDir'].'/')) {
                    mkdir($closureArgs['pullDir'].'/');
                }
                copy($filePath,$closureArgs['pullDir'].'/'.$fileName);
            },['pullDir'=>$pullDir]);
        }


        public function push(
            $dirName = null
            )
        {
            $dir = $dirName ?? '/';

            FileSys::traverse($this->venta->getBackend().$dir,function(
                $filePath,
                $fileName,
                $fileExtension,
                $closureArgs
            ){

                if ($fileName==='__venta.css.json') return;
                if ($fileName==='__venta.map.json') return;
                if ($fileExtension==='dir') {
                    $puller = new ReversionHandler($this->argv);
                    $puller->push('/'.$fileName);
                    return;
                }
                copy($filePath,$closureArgs['frontEnd'].'/'.$fileName);

            },['frontEnd'=>$this->venta->getFrontend().$dir,'backEnd'=>$this->venta->getBackend().$dir]);

            CoutStreamer::cout('Reverting build directory: '.$this->venta->getFrontEnd().$dir.'...');

        }



}
