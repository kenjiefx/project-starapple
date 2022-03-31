<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\BuilderFacadeInterface;
use \Kenjiefx\VentaCss\Build\CSS\CSSBuildManager;

class CSSBuilderFacade implements BuilderFacadeInterface{

    private string|null $namespace;
    private Venta $venta;
    private CSSBuildManager $cssBuildManager;

    public function __construct(
        string|null $namespace,
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->namespace = $this->venta->namespace;
        $this->cssBuildManager = new CSSBuildManager($this->venta);
    }

    public function build()
    {
        CoutStreamer::cout('Compressing venta/app.css...');
        $this->cssBuildManager->build();
    }

}
