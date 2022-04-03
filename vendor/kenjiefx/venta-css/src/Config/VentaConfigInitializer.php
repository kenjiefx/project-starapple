<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\ReversionHandler;

class VentaConfigInitializer {

    const CONFIG_PATH = '/venta.config.json';

    public static function hook (
        string $dir
        )
    {
        $path = ROOT.Self::CONFIG_PATH;
        if (!file_exists($path))
            Self::createApp($dir);

    }

    public static function load()
    {
        $path = ROOT.Self::CONFIG_PATH;
        try {
            if (!file_exists($path)) {
                throw new \Exception(
                    'Unable to find venta.config.json'
                );
            }
        } catch (\Exception $e) {
            CoutStreamer::cout('Error: '.$e->getMessage(),'error');
            exit();
        }
        return json_decode(file_get_contents($path),TRUE);
    }

    private static function createApp (
        string $dir
        )
    {
        $vntDir = ROOT.'/vnt';
        $configPath = ROOT.'/venta.config.json';
        if (!file_exists($vntDir))
            mkdir($vntDir);
        $dirDir = $vntDir.'/'.$dir;
        if (!file_exists($dirDir))
            mkdir($dirDir);
        $config = [
            'namespace'=>$dir,
            'ignore'=>[],
            'extensions'=>[]
        ];
        file_put_contents($configPath,json_encode($config));
    }

}
