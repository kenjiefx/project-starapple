<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class CSSModel {

    private array $css;
    private string $rawCss;

    public function __construct()
    {
        $this->css = [];
    }

    public function createSelector (
        string $selectorName
        )
    {
        $this->css[$selectorName] = [];
        return $this;
    }

    public function removeSelector (
        string $selectorName
        )
    {
        unset($this->css[$selectorName]);
        return $this;
    }

    public function setAttribute(
        string $selectorName,
        string $property,
        string $value
        )
    {
        $this->css[$selectorName][$property] = $value;
        return $this;
    }

    public function removeAttribute(
        string $className,
        string $property
        )
    {
        unset($this->css[$selectorName][$property]);
        return $this;
    }

    public function setRaw(
        string $rawCss
        )
    {
        $this->rawCss = $rawCss;
    }

    public function getRaw()
    {
        return $this->rawCss;
    }

    public function sort()
    {
        asort($this->css);
    }

    public function export()
    {
        return $this->css;
    }

}
