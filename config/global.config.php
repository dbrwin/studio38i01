<?php

return [
    "response" => [
        "language" => "ru",
    ],
    'view'    => [
        'adapter'      => 'twig',
        'templateDirs' => [
            'templates',
        ],
        'options'      => [
            'cache'       => 'data/cache',
            'auto_reload' => true,
        ],
        "extensions"   => [
            "\\Twig_Extension_Debug",
        ],
        "filters"      => [
            "cropBySpace" => [["\\DeltaUtils\\StringUtils", "cropBySpace"], ['pre_escape' => 'html']],
            "urlToTag" => [["\\DeltaUtils\\StringUtils", "urlToTag"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "nl2p"     => [["\\DeltaUtils\\StringUtils", "nl2p"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "cutStr"     => [["\\DeltaUtils\\StringUtils", "cutStr"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "nl2Array"     => [["\\DeltaUtils\\StringUtils", "nl2Array"], []],
            "idStr"     => [["\\DeltaUtils\\StringUtils", "toIdStr9"], []],
        ],
    ],
    "modules" => [
        "DeltaDb",
    ],
    "init"    => [
        "setLocale" => function($c) {
            setlocale(LC_ALL, 'ru_RU.utf8');
        }
    ],
];