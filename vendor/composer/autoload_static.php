<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit667d0213421234e6692e0b7fbdbd7021
{
    public static $files = array (
        '841780ea2e1d6545ea3a253239d59c05' => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
        'Q' => 
        array (
            'Qiniu\\' => 6,
        ),
        'P' => 
        array (
            'Parkour\\' => 8,
        ),
        'E' => 
        array (
            'Essence\\Http\\' => 13,
            'Essence\\Dom\\' => 12,
            'Essence\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
        'Qiniu\\' => 
        array (
            0 => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu',
        ),
        'Parkour\\' => 
        array (
            0 => __DIR__ . '/..' . '/fg/parkour/lib',
        ),
        'Essence\\Http\\' => 
        array (
            0 => __DIR__ . '/..' . '/essence/http/src',
        ),
        'Essence\\Dom\\' => 
        array (
            0 => __DIR__ . '/..' . '/essence/dom/src',
        ),
        'Essence\\' => 
        array (
            0 => __DIR__ . '/..' . '/essence/essence/lib/Essence',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit667d0213421234e6692e0b7fbdbd7021::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit667d0213421234e6692e0b7fbdbd7021::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
