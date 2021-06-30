<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit900afd81b55e0a5d50c05cf4de31cb52
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'WPUM_Extension_Activation' => __DIR__ . '/..' . '/wp-user-manager/wpum-extension-activation/wpum-extension-activation.php',
        'WP_Requirements_Check' => __DIR__ . '/..' . '/wearerequired/wp-requirements-check/WP_Requirements_Check.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit900afd81b55e0a5d50c05cf4de31cb52::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit900afd81b55e0a5d50c05cf4de31cb52::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit900afd81b55e0a5d50c05cf4de31cb52::$classMap;

        }, null, ClassLoader::class);
    }
}
