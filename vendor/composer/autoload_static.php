<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit31294a237c22e264ced2bb45f04ca7e1
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kenjiefx\\VentaCss\\' => 18,
            'Kenjie\\ProjectStarapple\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kenjiefx\\VentaCss\\' => 
        array (
            0 => __DIR__ . '/..' . '/kenjiefx/venta-css/src',
        ),
        'Kenjie\\ProjectStarapple\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit31294a237c22e264ced2bb45f04ca7e1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit31294a237c22e264ced2bb45f04ca7e1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit31294a237c22e264ced2bb45f04ca7e1::$classMap;

        }, null, ClassLoader::class);
    }
}
