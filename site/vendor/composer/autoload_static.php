<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3bf0b51cb1c1b15c688c1f80465b2562
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'ttm4135\\' => 8,
        ),
        'S' => 
        array (
            'Slim\\Views\\' => 11,
        ),
        'R' => 
        array (
            'ReCaptcha\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ttm4135\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Slim\\Views\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/views',
        ),
        'ReCaptcha\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/recaptcha/src/ReCaptcha',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3bf0b51cb1c1b15c688c1f80465b2562::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3bf0b51cb1c1b15c688c1f80465b2562::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3bf0b51cb1c1b15c688c1f80465b2562::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}