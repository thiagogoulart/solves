<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit054d458285ca892e74a628baa5b4f3a3
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        'b067bc7112e384b61c701452d53a14a8' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/JmesPath.php',
        'f7c6080b46f9fe41a46a657439674576' => __DIR__ . '/..' . '/kreait/firebase-php/src/Firebase.php',
    );

    public static $prefixLengthsPsr4 = array (
        'l' => 
        array (
            'libphonenumber\\' => 15,
        ),
        'S' => 
        array (
            'Superbalist\\Flysystem\\GoogleStorage\\' => 36,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Cache\\' => 10,
            'PagSeguro\\' => 10,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'L' => 
        array (
            'League\\Flysystem\\' => 17,
            'Lcobucci\\JWT\\' => 13,
        ),
        'K' => 
        array (
            'Kreait\\Firebase\\' => 16,
            'Kreait\\' => 7,
        ),
        'J' => 
        array (
            'JmesPath\\' => 9,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
            'Google\\Cloud\\Storage\\' => 21,
            'Google\\Cloud\\Core\\' => 18,
            'Google\\Auth\\' => 12,
            'Giggsey\\Locale\\' => 15,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
            'Firebase\\Auth\\Token\\' => 20,
            'Fig\\Http\\Message\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'libphonenumber\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/libphonenumber-for-php/src',
        ),
        'Superbalist\\Flysystem\\GoogleStorage\\' => 
        array (
            0 => __DIR__ . '/..' . '/superbalist/flysystem-google-storage/src',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'PagSeguro\\' => 
        array (
            0 => __DIR__ . '/..' . '/pagseguro/pagseguro-php-sdk/source',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'League\\Flysystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem/src',
        ),
        'Lcobucci\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/jwt/src',
        ),
        'Kreait\\Firebase\\' => 
        array (
            0 => __DIR__ . '/..' . '/kreait/firebase-php/src/Firebase',
        ),
        'Kreait\\' => 
        array (
            0 => __DIR__ . '/..' . '/kreait/gcp-metadata/src',
        ),
        'JmesPath\\' => 
        array (
            0 => __DIR__ . '/..' . '/mtdowling/jmespath.php/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Google\\Cloud\\Storage\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/cloud-storage/src',
        ),
        'Google\\Cloud\\Core\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/cloud-core/src',
        ),
        'Google\\Auth\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/auth/src',
        ),
        'Giggsey\\Locale\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/locale/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Firebase\\Auth\\Token\\' => 
        array (
            0 => __DIR__ . '/..' . '/kreait/firebase-tokens/src',
        ),
        'Fig\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/fig/http-message-util/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Rize\\UriTemplate' => 
            array (
                0 => __DIR__ . '/..' . '/rize/uri-template/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit054d458285ca892e74a628baa5b4f3a3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit054d458285ca892e74a628baa5b4f3a3::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit054d458285ca892e74a628baa5b4f3a3::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
