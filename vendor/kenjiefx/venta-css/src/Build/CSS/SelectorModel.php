<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class SelectorModel {

    public string $realName;
    public string $minifiedName;

    public string $prefixer;
    public string $typeOf;

    public SelectorModel|null $parentOf;
    public SelectorModel|null $childOf;

    # Pseudo Class
    public bool $hasPseudo;
    public string|null $pseudoClass;
    public string|null $pseudoSeparator;

    public function __construct(
        string $realName
        )
    {
        $this->realName = $realName;
        $this->prefixer = '';
        $this->hasPseudo = false;
        $this->pseudoClass = null;
        $this->pseudoSeparator = null;
        $this->parentOf = null;
        $this->childOf = null;
        $this->parseType();
    }

    public function minifyName(
        array $registrar
        )
    {

        switch ($this->typeOf) {

            /**
             * Universal Selector
             * For universal selector, we do not have to register
             * a minified name, as we do not need to re-use it
             * across the CSS file
             */
            case 'universal':
                $this->minifiedName = '*';
                break;

            /**
             * Element Selector
             * While element selectors are re-usable, we do not need
             * to provide a minified name to them as they can't be
             * modified in the HTML
             */
            case 'element':
                $this->minifiedName = $this->realName;
                break;

            # Class and ID Selectors
            default:
                $this->minifiedName = Utils::createClassName($registrar);
                break;
        }

    }

    public function rectifyName()
    {
        return $this->prefixer.$this->minifiedName;
    }

    private function parseType()
    {
        if (str_contains($this->realName,':')) {
            $this->registerPseudos(':');
        }
        if (str_contains($this->realName,'::')) {
            $this->registerPseudos('::');
        }
        if (str_contains($this->realName,'.')) {
            $this->prefixer = '.';
            $this->typeOf = 'class';
            return;
        }
        if (str_contains($this->realName,'#')) {
            $this->prefixer = '#';
            $this->typeOf = 'id';
            return;
        }
        if (str_contains($this->realName,'*')) {
            $this->typeOf = 'universal';
            return;
        }
        $this->typeOf = 'element';
        return;
    }

    private function registerPseudos(
        string $separator
        )
    {
        $this->hasPseudo       = true;
        $this->pseudoSeparator = $separator;
        $this->pseudoClass     = explode($separator,$this->realName)[1];
        $this->realName        = explode($separator,$this->realName)[0];
    }





}
