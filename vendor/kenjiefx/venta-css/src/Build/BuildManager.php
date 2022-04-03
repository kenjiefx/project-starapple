<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Build\BuilderFacadeInterface;
use \Kenjiefx\VentaCss\Build\CSS\CSSBuilderFacade;
use \Kenjiefx\VentaCss\Build\HTML\HTMLBuilderFacade;
use \Kenjiefx\VentaCss\Build\Compiler\Compiler;

class BuildManager implements BuilderFacadeInterface {

    private string|null $namespace;
    private CSSBuilderFacade $CSSBuilder;
    private HTMLBuilderFacade $HTMLBuilder;
    private Compiler $Compiler;
    private int $startTime;

    public function __construct(
        array $argv
        )
    {
        $this->startTime = microtime(true);
        $this->namespace = $argv[2] ?? null;
        $this->loadTools();
    }

    /**
     * @throws Exception
     * When namespace isn't found
     */
    public function loadTools()
    {
        $this->venta = new Venta($this->namespace);
        $this->CSSBuilder = new CSSBuilderFacade(
            $this->namespace,
            $this->venta
        );
        $this->HTMLBuilder = new HTMLBuilderFacade(
            $this->namespace,
            $this->venta
        );
        $this->Compiler = new Compiler(
            $this->namespace,
            $this->venta
        );
    }


    public function build()
    {
        $timeStart = microtime(true);

        $this->CSSBuilder->build();
        $this->HTMLBuilder->build();
        $this->Compiler->compile();

        CoutStreamer::cout('Successfully compressed files!','success');

        $newFileSize = FileSys::getSize($this->venta->getFrontend().'/venta/app.css');
        CoutStreamer::cout('Total build time: '.(microtime(true)-$timeStart).' seconds');

    }



}
