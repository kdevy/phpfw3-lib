<?php

/**
 * Kdevy/Phpfw3Lib : My original php framework. (https://github.com/kdevy/phpfw3-lib)
 * Copyright (c) Yoshiki Kinoshita.
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright     Copyright (c) Yoshiki Kinoshita.
 * @link          https://github.com/kdevy/phpfw3-lib
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

use Kdevy\Phpfw3Lib\Route;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    /**
     * @return void
     *
     * @dataProvider getRequestPathModulePatterns
     */
    public function testGetModuleName($expected, $requestPath): void
    {
        $route = new Route($requestPath);
        $this->assertSame($expected, $route->getModuleName());
    }

    /**
     * @return array
     */
    public function getRequestPathModulePatterns(): array
    {
        $psr17Factory = new Psr17Factory();

        return [
            // Pattern string.
            ["index", ""],
            ["index", "/"],
            ["index", "/action"],
            ["module", "/module/"],
            ["module", "/module/action"],
            ["module", "  /module/action  "],
            // Pattern array.
            ["index", []],
            ["index", [""]],
            ["index", ["action"]],
            ["module", ["module", ""]],
            ["module", ["module", "action"]],
            ["module", ["  module  ", "  action  "]],
            // Pattern ServerRequest.
            ["index", $psr17Factory->createServerRequest("GET", "/")],
            ["index", $psr17Factory->createServerRequest("GET", "/action")],
            ["module", $psr17Factory->createServerRequest("GET", "/module/")],
            ["module", $psr17Factory->createServerRequest("GET", "/module/action")],
        ];
    }
}
