<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\ClassModel;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class CSSBuildManager {

    private Venta $venta;
    private CSSModel $ParsedCSS;
    private CSSModel $RefinedCss;
    private string $css;
    private array $registrar;
    private array $compiled;
    private array $reference;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->registrar = [];
        $this->compiled = [];
        $this->reference = [];
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

        //echo json_encode($this->registrar).PHP_EOL.PHP_EOL;

        CoutStreamer::cout('Compressing class names...');
        $this->reduce();

        //echo json_encode($this->registrar).PHP_EOL.PHP_EOL;
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
        # Parsing every selector entity in a group of selectors
        $selectorGroups = explode(',',$selectorName);

        foreach ($selectorGroups as $selectorGroup) {

            # Parsing every selector entity in a family of selectors
            $selectorFamily = explode(' ',trim($selectorGroup));

            $parentOf = null;
            $childOf = null;

            $numberOfFamilyMember = count($selectorFamily);
            $familyMemberIterator = 1;

            /**
             * Looping through each of the family members!
             * We neen to make sure that in a certain CSS selector
             * only the last child declared will receive the rules.
             * Here, we also make sure that every family generation
             * is registered in the registrar array
             */
            foreach ($selectorFamily as $selector) {

                $selectorObj = new SelectorModel(trim($selector));
                $selectorObj->minifyName($this->registrar);
                $selectorObj->rules = [];

                # Registering parent name and child name
                if (null!==$childOf) {
                    $selectorObj->childOf = new SelectorModel($childOf);
                }

                # Making sure that only the last child declared in the selector
                # Will register the rules given to that selector
                if ($familyMemberIterator===$numberOfFamilyMember) {
                    $selectorObj->rules = $rules;
                }

                $childOf = trim($selector);

                # Saving the Selector object to the Registrar array
                $this->registrar[$selectorObj->minifiedName] = $selectorObj;

                $familyMemberIterator++;

            }
        }
    }

    /**
     * After the main CSS file was parsed, and individual selectors
     * are given minified name, sorted out, and segragated into separate
     * objects in the Registrar, this method will further reduce the Registrar
     * by eliminating literraly the same selector, but has different rules given
     *
     * Sometimes, when we write CSS, we give different values to the same selectors
     * in the following way:
     *
     * .element {margin: 12px;}
     * .element1, h1 {font-size: 8px;}
     *
     * This method will further reduce the '.element' entries in the Registrar
     * into one CONSOLIDATED registrar entry, like so:
     * "exm":{"rules":{"margin":"12px";"font-size":"12px;""}}
     */
    public function reduce()
    {
        $registrar = $this->registrar;
        $reduced = [];
        foreach ($registrar as $AminifiedName => $AselectorObj) {

            /**
             * Before we save collate existing rules, we will check if the
             * same selector has already been recorded.
             */
            $isExisting = false;
            foreach ($reduced as $RminifiedName => $RselectorObj) {
                if ($RselectorObj->realName===$AselectorObj->realName) {
                    if ($RselectorObj->hasPseudo) {
                        if (!$AselectorObj->hasPseudo) continue;
                        if ($RselectorObj->pseudoClass!==$AselectorObj->pseudoClass) continue;
                    }
                    $isExisting = true;
                    break;
                }
            }

            if ($isExisting) continue;

            $matchedRules = [];

            /**
             * On this part of the code, we collate all the existing rules
             * given to the selector of the same name
             */
            foreach ($this->registrar as $BminifiedName => $BselectorObj) {
                /**
                 * @TODO: Need to consider pseudoclasses
                 * We have updated the selector model to automatically
                 * remove the pseudo class from the selector name,
                 * hence the following comparison might not be always
                 * true
                 */
                if ($AselectorObj->hasPseudo) {
                  if (!$BselectorObj->hasPseudo) continue;
                  if ($AselectorObj->pseudoClass!==$BselectorObj->pseudoClass) continue;
                }
                if ($AselectorObj->realName==$BselectorObj->realName) {
                    foreach ($BselectorObj->rules as $property => $value) {
                        # Recording each matching rules to consolidate later
                        $matchedRules[$property] = $value;
                    }
                }

            }

            foreach ($matchedRules as $property => $value) {
                $AselectorObj->rules[$property] = $value;
            }

            $reduced[$AminifiedName] = $AselectorObj;
        }

        $this->registrar = $reduced;

    }

    /**
     * Compilation works by eliminating two or more different selectors
     * but litterally has the same rules! A reference dataset will also
     * be compiled so that we will know what CSS selector we gave
     */
    public function compile()
    {

        /**
         * Cases when we  do not include a certain selector in the Registry
         * to our compilation:
         */
        $toCompile = true;

        foreach ($this->registrar as $minifiedName => $selectorObj) {

            $this->addReference(
                $selectorObj->realName,
                '',
                $selectorObj->prefixer,
                $selectorObj->typeOf
            );

            $matchingRules   = [];
            $unMatchedRules  = $selectorObj->rules;
            $hasMatchingRule = false;


            foreach ($this->compiled as $CminifiedName => $CselectorObj) {

                /**
                 * 1. We can only conclude if the rules has already existed in
                 * our compiled dataset if their selectors are of the same time
                 *
                 * @example
                 * h1 {font:size:10px;} CANNOT combine with .title{font-size:10px;}
                 */
                if ($CselectorObj->typeOf!==$selectorObj->typeOf) continue;

                if ($CselectorObj->hasPseudo)
                {
                    if ($CselectorObj->pseudoClass!==$selectorObj->pseudoClass) continue;
                }

                if (null!==$CselectorObj->childOf&&null!==$selectorObj->childOf) {
                    if ($CselectorObj->childOf->realName!==$selectorObj->childOf->realName) continue;
                }


                $matchingRulesCount = 0;


                foreach ($selectorObj->rules as $property => $value) {
                    $Cvalue = $CselectorObj->rules[$property] ?? null;
                    if ($Cvalue===$value) {
                        $hasMatchingRule = true;
                        unset($unMatchedRules[$property]);
                        $matchingRulesCount++;
                    }
                }


                if ($hasMatchingRule) {

                    /**
                     * When the selector has exactly the same rules with
                     * another selector, but they were given a different
                     * selector name.
                     *
                     * We do not compile this to our final CSS, but we would
                     * reference this in our reference dataset
                     */
                    if ($matchingRulesCount===count($CselectorObj->rules)) {
                        # $this->reference[$selectorObj->realName] .= ' '.$CminifiedName;
                        $this->addReference(
                            $selectorObj->realName,
                            $CminifiedName,
                            $CselectorObj->prefixer,
                            $CselectorObj->typeOf
                        );
                        $toCompile = false;
                        continue;
                    }

                }
            }

            /**
             * Every unmatched rules will be processed under a new selector name
             * @example
             * .wrapper {margin:10px}
             * .container {margin:10px;padding:20px;}
             *
             * The unmatched rule, which is padding:20px, would be compiled
             * into a separate selector, i.e.:
             * .newSelector {padding:20px;}
             *
             */
            if (count($unMatchedRules)>0&&$hasMatchingRule===true) {
                $proxySelector = new SelectorModel($selectorObj->realName);
                $proxySelector->minifyName($this->registrar);
                $proxySelector->rules = $unMatchedRules;
                $this->compiled[$proxySelector->minifiedName] = $proxySelector;
                $this->addReference(
                    $selectorObj->realName,
                    $proxySelector->minifiedName,
                    $proxySelector->prefixer,
                    $proxySelector->typeOf
                );
            }

            if (count($unMatchedRules)===0&&$hasMatchingRule===true) {
                if ($this->reference[$selectorObj->realName]['html']==='') {
                    $this->addReference(
                        $selectorObj->realName,
                        $minifiedName,
                        $selectorObj->prefixer,
                        $selectorObj->typeOf
                    );
                    $this->compiled[$minifiedName] = $selectorObj;
                }
            }


            if (!$hasMatchingRule) {
                $this->addReference(
                    $selectorObj->realName,
                    $minifiedName,
                    $selectorObj->prefixer,
                    $selectorObj->typeOf
                );
                $this->compiled[$minifiedName] = $selectorObj;
            }


        }
    }

    public function export()
    {
        $forExport = [];
        foreach ($this->compiled as $minifiedName => $selectorObj) {
            $minifiedName = $selectorObj->prefixer.$minifiedName;
            if ($selectorObj->hasPseudo) {
                $forExport[$minifiedName.$selectorObj->pseudoSeparator.$selectorObj->pseudoClass] = $selectorObj->rules;
                continue;
            }
            if (null!==$selectorObj->childOf) {
                $parentMinifiedName = $this->reference[$selectorObj->childOf->realName]['css'];
                $forExport[$parentMinifiedName.' '.$minifiedName] = $selectorObj->rules;
                continue;
            }
            if (empty($selectorObj->rules)) {
                continue;
            }

            $forExport[$minifiedName] = $selectorObj->rules;
        }
        return $forExport;
    }


    private function sortRegistrar()
    {
        $scraped = [];
        $sorted = [];
        foreach ($this->registrar as $minifiedName => $selectorObj) {
            $scraped[$minifiedName] = $selectorObj->rules;
        }
        asort($scraped);
        foreach ($scraped as $minifiedName => $value) {
            $sorted[$minifiedName] = $this->registrar[$minifiedName];
        }
        $this->registrar = $sorted;
    }


    public function addReference(
        string $realName,
        string $minifiedName,
        string $prefixer,
        string $typeOf
        )
    {
        if (!isset($this->reference[$realName])) {
            $this->reference[$realName] = [
                'html' => '',
                'css' => '',
                'typeOf' => ''
            ];
        }
        if ($this->reference[$realName]['html']=='') {
            $this->reference[$realName] = [
                'html' => $minifiedName,
                'css' => $prefixer.$minifiedName,
                'typeOf' => $typeOf
            ];
            return;
        }
        $this->reference[$realName] = [
            'html' => $this->reference[$realName]['html'].' '.$minifiedName,
            'css' => $this->reference[$realName]['css'].$prefixer.$minifiedName,
            'typeOf' => $typeOf
        ];
        return;
    }

    public function getReference()
    {
        return $this->references;
    }

    public function release()
    {

        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.css.json',
            json_encode($this->reference)
        );
        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.map.json',
            json_encode($this->export())
        );
    }

}
