<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\ClassModel;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;
use \Kenjiefx\VentaCss\Build\CSS\Utils;
use \Kenjiefx\VentaCss\Build\CSS\SelectorMatcher as Matching;

class CSSBuildManager {

    private Venta $venta;
    private CSSModel $ParsedCSS;
    private CSSModel $RefinedCss;
    private string $css;
    private array $theRegistrar;
    private array $theTracker;
    private array $compiled;
    private array $reference;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->theRegistrar = [];
        $this->theTracker = [];
        $this->theCompiled = [];
        $this->theReference = [];
    }

    public function build()
    {
        # First, we set the raw CSS file: venta/app.css
        $this->ParsedCSS->setRaw(
            rawCss: $this->venta->getCssToBuild()
        );

        # Next, we parse the raw CSS into an array
        Utils::parseRawCss($this->ParsedCSS);

        # Then, we sort the CSS array
        $this->ParsedCSS->sort();

        # Then, we register each of the CSS class
        foreach ($this->ParsedCSS->export() as $selector => $rules) {
            $this->register($selector,$rules);
        }



        CoutStreamer::cout('Compressing class names...');
        $this->reduce();
        $this->sortRegistrar();
        $this->compile();

        CoutStreamer::cout('Saving venta/app.css...');
        $this->release();

        // echo json_encode($this->compiled).PHP_EOL.PHP_EOL;
        // echo json_encode($this->reference).PHP_EOL.PHP_EOL;
        // echo json_encode($this->export()).PHP_EOL.PHP_EOL;

    }


    private function register(
        string $selectorName,
        array $rules
        )
    {
        $selectorObj = new SelectorModel(trim($selectorName));
        $selectorObj->rules = $rules;
        array_push($this->theRegistrar,$selectorObj);
    }

    /**
     * After the main CSS file was parsed, and individual selectors
     * are given minified name, sorted out, and segragated into separate
     * objects in the Registrar, this method will further reduce the Registrar
     * by eliminating literraly the same selector, but has different rules given
     *
     */
    public function reduce()
    {
        $TheRegistrar = $this->theRegistrar;
        $reduced = [];

        foreach ($TheRegistrar as $A) {

            /**
             * Before we save collate existing rules, we will check if the
             * same selector has already been recorded.
             *
             * The rules for two selectors to be considered as the same
             * are the following
             * 1. They must have the same pseudo type
             */
            $isExisting = false;
            $A->minifyName($this->theTracker);

            foreach ($reduced as $key => $R) {
                if (Matching::RealSelectorNames($A,$R))
                    $A->setMinifiedName($R->minifiedName);
                if (!Matching::RealSelectorNames($A,$R))
                    continue;
                if (!Matching::PseudoClassNames($A,$R))
                    continue;
                foreach ($A->rules as $property => $value)
                    $reduced[$key]->rules[$property] = $value;
                $isExisting = true;
                break;
            }

            if (!$isExisting)
                array_push($reduced,$A);
        }

        $this->theRegistrar = $reduced;

    }

    /**
     * Compilation works by eliminating two or more different selectors
     * but litterally has the same rules! A reference dataset will also
     * be compiled so that we will know what CSS selector we gave
     */
    public function compile()
    {

        foreach ($this->theRegistrar as $A) {
            $hasMatchingRule = false;

            foreach ($this->theCompiled as $key => $C) {
                if (Matching::RealSelectorNames($A,$C))
                    continue;
                if (!Matching::HasPseudoSelectors($A,$C))
                    continue;
                if (!Matching::PseudoClassNames($A,$C))
                    continue;
                $matchCount = 0;

                foreach ($C->rules as $prop => $val) {
                    if (isset($A->rules[$prop])) {
                        if ($A->rules[$prop]===$val) {
                            $A->rules[$prop] = null;
                            $matchCount++;
                        }
                    }
                }

                if (count($A->rules)===$matchCount) {
                    $A->toRender     = false;
                    $hasMatchingRule = true;
                    $this->addReference(
                        realName: $A->realName,
                        minifiedName: $C->minifiedName
                    );
                    array_push($this->theCompiled,$A);
                    break;
                }

                if ($matchCount>0)
                    $this->addReference(
                        realName: $A->realName,
                        minifiedName: $C->minifiedName
                    );

            }

            if(!$hasMatchingRule) {
                $this->addReference(
                    realName: $A->realName,
                    minifiedName: $A->minifiedName
                );
                array_push($this->theCompiled,$A);
            }
        }

    }




    private function sortRegistrar()
    {
        $scraped = [];
        $sorted = [];
        foreach ($this->theRegistrar as $key => $Sobj) {
            $scraped['x'.$key] = $Sobj->rules;
        }
        asort($scraped);
        foreach ($scraped as $key => $value) {
            $rKey = intval(substr($key,1));
            array_push($sorted,$this->theRegistrar[$rKey]);
        }
        $this->theRegistrar = $sorted;
    }


    public function addReference(
        string $realName,
        string $minifiedName
        )
    {
        $minifiedList = [];
        if (isset($this->theReference[$realName])) {
            $minifiedList = explode(' ',$this->theReference[$realName]);
        }
        if (!in_array($minifiedName,$minifiedList)) {
            array_push($minifiedList,$minifiedName);
        }
        $this->theReference[$realName] = implode(' ',$minifiedList);
        return;
    }

    public function getReference()
    {
        return $this->references;
    }

    public function export()
    {
        $exported = [];
        foreach ($this->theCompiled as $selectorObj) {
            $finalName = $selectorObj->minifiedName;
            if ($selectorObj->hasPseudo)
                $finalName .= $selectorObj->pseudoSeparator.$selectorObj->pseudoClass;
            foreach ($selectorObj->rules as $prop => $val)
                if (null!==$val)
                    $exported[$selectorObj->minifiedName]['css'][$finalName][$prop] = $val;
        }
        return $exported;
    }

    public function release()
    {

        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.css.json',
            json_encode($this->export())
        );
        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.map.json',
            json_encode($this->theReference)
        );
    }

}
