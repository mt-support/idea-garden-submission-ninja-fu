<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfc15b9d37a72e80978bf3ed93d70ae63
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modern_Tribe\\Idea_Garden\\Ninja_Fu\\' => 34,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modern_Tribe\\Idea_Garden\\Ninja_Fu\\' => 
        array (
            0 => __DIR__ . '/../..' . '/php',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfc15b9d37a72e80978bf3ed93d70ae63::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfc15b9d37a72e80978bf3ed93d70ae63::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
