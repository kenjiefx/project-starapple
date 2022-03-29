<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\CSS\Utils;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;

class ClassModel {

    private SelectorModel $selector;
    private string $className;

    public function __construct(
        string $selector
        )
    {
        $this->selector = new SelectorModel($selector);
    }

    public function getName()
    {
        return $this->className;
    }





}
