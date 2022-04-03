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
        FileSys::traverse($this->venta->getFrontend().$dir,function(
            $filePath,
            $fileName,
            $fileExtension,
            $closureArgs
        ){
            if ($fileExtension==='html'||$fileExtension==='htm'||$fileExtension==='php') {

                require_once __dir__.'/simple_html_dom.php';

                $dom = file_get_html($filePath);
                $References = json_decode(file_get_contents($closureArgs['backEnd'].'/venta/__venta.map.json'),TRUE);

                foreach ($References as $realName => $reference) {
                    foreach($dom->find($realName) as $element) {
                        $className = substr($realName,1);
                        $element->removeClass($className);
                        $element->addClass($reference);
                        $element->removeClass('1');
                        $this->addToUsables($reference);
                    }
                }

                $dom->save($filePath);
            }

            if ($fileExtension==='dir') {
                $this->build('/'.$fileName);
            }

        },['frontEnd'=>$this->venta->getFrontend(),'backEnd'=>$this->venta->getBackend()]);
    }

    private function addToUsables(
        string $htmlRef
        )
    {
        $tokens = explode(' ',$htmlRef);
        foreach ($tokens as $token)
            if (!in_array($token,$this->usables))
                array_push($this->usables,$token);
    }

    public function getUsables()
    {
        return $this->usables;
    }

    public function logUsables()
    {
        file_put_contents($this->venta->getBackend().'/venta/__venta.usables.json',json_encode($this->usables));
    }

}
