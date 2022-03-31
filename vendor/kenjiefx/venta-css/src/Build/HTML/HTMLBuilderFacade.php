<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\HTMLBuilderManager;

class HTMLBuilderFacade {

    private string|null $namespace;
    private Venta $venta;
    private HTMLBuilderManager $htmlBuilderManager;

    public function __construct(
        string|null $namespace,
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->namespace = $this->venta->namespace;
        $this->htmlBuilderManager = new HTMLBuilderManager($this->venta);
    }

    public function build()
    {
        CoutStreamer::cout('Rendering new selectors in HTML...');
        $this->htmlBuilderManager->build();
        $this->htmlBuilderManager->createAppCss();
    }



}
