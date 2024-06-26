<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'chart.js/auto' => [
        'version' => '3.9.1',
    ],
    'vue' => [
        'version' => '3.4.15',
    ],
    '@vue/runtime-dom' => [
        'version' => '3.4.15',
    ],
    '@vue/compiler-dom' => [
        'version' => '3.4.21',
    ],
    '@vue/shared' => [
        'version' => '3.4.15',
    ],
    '@vue/runtime-core' => [
        'version' => '3.4.15',
    ],
    '@vue/compiler-core' => [
        'version' => '3.4.21',
    ],
    '@vue/reactivity' => [
        'version' => '3.4.15',
    ],
    '@symfony/ux-vue' => [
        'path' => './vendor/symfony/ux-vue/assets/dist/loader.js',
    ],
    'axios' => [
        'version' => '1.7.2',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
    '@vuelidate/validators' => [
        'version' => '2.0.4',
    ],
    'vue-demi' => [
        'version' => '0.13.11',
    ],
    '@vuelidate/core' => [
        'version' => '2.0.3',
    ],
];
