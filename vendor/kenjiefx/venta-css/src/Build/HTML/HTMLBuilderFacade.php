<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\HTMLBuilderManager;

class HTMLBuilderFacade {

    private string $namespace;
    private Venta $venta;
    private HTMLBuilderManager $htmlBuilderManager;

    public function __construct(
        string $namespace,
        Venta $venta
        )
    {
        $this->namespace = $namespace;
        $this->venta = $venta;
        $this->htmlBuilderManager = new HTMLBuilderManager($this->venta);
    }

    public function build()
    {
        CoutStreamer::cout('Rendering new selectors in HTML...');
        $this->htmlBuilderManager->build();
        $this->htmlBuilderManager->createAppCss();
    }



}
