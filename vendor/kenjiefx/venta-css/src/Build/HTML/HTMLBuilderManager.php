<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;


class HTMLBuilderManager {

    private Venta $venta;
    private array $usables;
    private array $compiled;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->usables = [];
        $this->compiled = [];
    }

    public function build(
        $dirPath = null
        )
    {

        $dir = $dirPath ?? '/';
        $this->getCompiledCss();

        FileSys::traverse($this->venta->getFrontend().$dir,function(
            $filePath,
            $fileName,
            $fileExtension,
            $closureArgs
        ){
            if ($fileExtension==='html'||$fileExtension==='htm'||$fileExtension==='php') {
                require_once __dir__.'/simple_html_dom.php';
                $dom = file_get_html($filePath);
                $references = json_decode(file_get_contents($closureArgs['backEnd'].'/venta/__venta.css.json'),TRUE);

                foreach ($references as $realName => $reference) {
                    if ($reference['typeOf']==='universal'){
                        $this->addToUsables($reference['html']);
                        continue;
                    }
                    if ($reference['typeOf']==='element'){
                        $this->addToUsables($reference['html']);
                        continue;
                    }
                    foreach($dom->find($realName) as $element) {
                        if ($reference['typeOf']==='class') {
                            $realName = substr($realName,1);
                        }
                        if ($reference['typeOf']==='id') {
                            $realName = substr($realName,1);
                        }
                        $element->removeClass($realName);
                        $element->addClass($reference['html']);
                        $element->removeClass('1');
                        $this->addToUsables($reference['html']);
                    }
                }

                $dom->save($filePath);

            }

            if ($fileExtension==='dir') {
                $this->build('/'.$fileName);
            }


        },['frontEnd'=>$this->venta->getFrontend(),'backEnd'=>$this->venta->getBackend()]);


    }

    private function getCompiledCss()
    {
        $this->compiled = json_decode(file_get_contents($this->venta->getBackend().'/venta/__venta.map.json'),TRUE);
    }

    private function addToUsables(
        string $htmlRef
        )
    {
        /**
         * @TODO Create a way to only export css that has been used throughout the project
         */
        $refs = explode(' ',$htmlRef);
        foreach ($refs as $ref) {
            foreach ($this->compiled as $selector => $rules) {
                if (str_contains($selector,$ref)) {
                    $this->usables[$selector] = $rules;
                }
            }
        }
    }

    public function createAppCss()
    {
        $css = '';
        foreach ($this->usables as $selector => $rules) {
            $css = $css.$selector.'{';
            foreach ($rules as $property => $value) {
                $css = $css.$property.':'.$value.';';
            }
            $css = $css.'} ';
        }
        file_put_contents($this->venta->getFrontend().'/venta/app.css',$css);
    }

}
