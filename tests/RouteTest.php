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

use Kdevy\Phpfw3Lib\Exceptions\RouteParseError;
use Kdevy\Phpfw3Lib\Route;
use Kdevy\Phpfw3Lib\Utils\StringUtil;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RouteTest extends TestCase
{
    const ROUTE_PARSE_ERROR_MODULE_NAME_MESSAGE = "Invalid for the module name '%{name}'.";
    const ROUTE_PARSE_ERROR_ACTION_NAME_MESSAGE = "Invalid for the action name '%{name}'.";
    /**
     * モジュール名として有効/無効な文字列は isValidModuleName のドキュメントを参照して下さい。
     *
     * @return void
     */
    public function testIsValidModuleName(): void
    {
        /** 正常ケース */
        $this->assertTrue(Route::isValidModuleName("module"));      // アルファベットのみ
        $this->assertTrue(Route::isValidModuleName("_-module"));    // アンダースコア、ハイフンを含む
        /** 異常ケース */
        $this->assertFalse(Route::isValidModuleName("あああ"));      // 全角文字を含む
        $this->assertFalse(Route::isValidModuleName("%module"));    // 使用できない記号を含む
        $this->assertFalse(Route::isValidModuleName("  module  "));
    }

    /**
     * アクション名として有効/無効な文字列は isValidActionName() のドキュメントを参照して下さい。
     *
     * @return void
     */
    public function testIsValidActionName(): void
    {
        /** 正常ケース */
        $this->assertTrue(Route::isValidActionName("action"));      // アルファベットのみ
        $this->assertTrue(Route::isValidActionName("_action"));     // 先頭がアンダースコア
        $this->assertTrue(Route::isValidActionName("action_"));     // 先頭以外にアンダースコア
        $this->assertTrue(Route::isValidActionName("action123"));   // 先頭以外に数字を含む
        /** 異常ケース */
        $this->assertFalse(Route::isValidActionName("あああ"));      // 全角文字を含む
        $this->assertFalse(Route::isValidActionName("123action"));  // 先頭が数字
        $this->assertFalse(Route::isValidActionName("%action"));    // 先頭に使用できない記号を含む
        $this->assertFalse(Route::isValidActionName("-action"));
        $this->assertFalse(Route::isValidActionName("action%"));    // 先頭以外に使用できない記号を含む
        $this->assertFalse(Route::isValidActionName("action-"));
        $this->assertFalse(Route::isValidActionName("  action  "));
    }

    /**
     * @return void
     */
    public function testFormatName(): void
    {
        $this->assertSame("hoge", Route::formatName(" hoge "));         // トリム
        $this->assertSame("hoge", Route::formatName("HoGe"));           // 小文字変換
        $this->assertSame("hoge", Route::formatName("../hoge"));        // ディレクトリトラバーサルのエスケープ
        $this->assertSame("hoge", Route::formatName("  ../HoGe  "));    // 組み合わせ
    }

    /**
     * @param string $expected
     * @param string|array|ServerRequestInterface $requestPath
     * @return void
     * 
     * @dataProvider getRequestPathModuleNamePatterns
     */
    public function testGetModuleName(string $expected, string|array|ServerRequestInterface $requestPath): void
    {
        $route = new Route($requestPath);
        $this->assertSame($expected, $route->getModuleName());
    }

    /**
     * @param string $expected
     * @param string|array|ServerRequestInterface $requestPath
     * @return void
     * 
     * @dataProvider getRequestPathActionNamePatterns
     */
    public function testGetActionName(string $expected, string|array|ServerRequestInterface $requestPath): void
    {
        $route = new Route($requestPath);
        $this->assertSame($expected, $route->getActionName());
    }

    /**
     * @return void
     */
    public function testOccurRouteParseErrorByModuleName(): void
    {
        $this->expectException(RouteParseError::class);
        $this->expectExceptionMessage(StringUtil::format(self::ROUTE_PARSE_ERROR_MODULE_NAME_MESSAGE, ["name" => "モジュール"]));
        new Route("/モジュール/action");
    }

    /**
     * @return void
     */
    public function testOccurRouteParseErrorByActionName(): void
    {
        $this->expectException(RouteParseError::class);
        $this->expectExceptionMessage(StringUtil::format(self::ROUTE_PARSE_ERROR_ACTION_NAME_MESSAGE, ["name" => "アクション"]));
        new Route("/module/アクション");
    }

    /**
     * @return void
     * 
     * @doesNotPerformAssertions
     */
    public function testMultiByteStringInDeepPath(): void
    {
        new Route("/module/action/ほげ");
    }

    /**
     * getRequestPathPatterns()のパターンリストとそのモジュール名の期待値を取得します。
     *
     * @return array
     */
    public function getRequestPathModuleNamePatterns(): array
    {
        $requestPathPatterns = $this->getRequestPathPatterns();
        $expectedModuleNames = [
            // 文字列
            "index",
            "index",
            "index",
            "index",
            "module",
            "module",
            "module",
            "module",
            "module",
            "module",
            // 配列
            "index",
            "index",
            "index",
            "module",
            "module",
            "module",
            // オブジェクト
            "index",
            "index",
            "index",
            "index",
            "module",
            "module",
            "module",
            "module",
            "module",
            "module",
        ];

        return array_map(
            fn ($expectedModuleName, $requestPath) => [$expectedModuleName, $requestPath],
            $expectedModuleNames,
            $requestPathPatterns
        );
    }

    /**
     * getRequestPathPatterns()のパターンリストとそのアクション名の期待値を取得します。
     * 
     * @return array
     */
    public function getRequestPathActionNamePatterns(): array
    {
        $requestPathPatterns = $this->getRequestPathPatterns();
        $expectedActionNames = [
            // 文字列
            "index",
            "index",
            "action",
            "action",
            "index",
            "index",
            "action",
            "action",
            "action",
            "action",
            // 配列
            "index",
            "index",
            "action",
            "index",
            "action",
            "action",
            // オブジェクト
            "index",
            "index",
            "action",
            "action",
            "index",
            "index",
            "action",
            "action",
            "action",
            "action",
        ];

        return array_map(
            fn ($expectedModuleName, $requestPath) => [$expectedModuleName, $requestPath],
            $expectedActionNames,
            $requestPathPatterns
        );
    }

    /**
     * Routeクラスコンストラクタのリクエストパスとして渡されうる値のパターンリストを取得します。
     *
     * @return array
     */
    public function getRequestPathPatterns(): array
    {
        $psr17Factory = new Psr17Factory();

        return [
            // 文字列
            "/",                                // モジュール指定なし, アクション指定なし, パラメータなし
            "/?key=value",                      // モジュール指定なし, アクション指定なし, パラメータあり
            "/action",                          // モジュール指定なし, アクション指定あり, パラメータなし
            "/action?key=value",                // モジュール指定なし, アクション指定あり, パラメータあり
            "/module/",                         // モジュール指定あり, アクション指定なし, パラメータなし
            "/module/?key=value",               // モジュール指定あり, アクション指定なし, パラメータあり
            "/module/action",                   // モジュール指定あり, アクション指定あり, パラメータなし
            "/module/action?key=value",         // モジュール指定あり, アクション指定あり, パラメータあり
            "/module/action/hoge",              // モジュール指定あり, アクション指定あり, パラメータなし, 深すぎる階層
            "/module/action/hoge?key=value",    // モジュール指定あり, アクション指定あり, パラメータあり, 深すぎる階層
            // 配列（配列の場合はパラメータ文字列は含まれていない前提）
            [],                                 // モジュール指定なし, アクション指定なし
            [""],                               // モジュール指定なし, アクション指定なし
            ["action"],                         // モジュール指定なし, アクション指定あり
            ["module", ""],                     // モジュール指定あり, アクション指定なし
            ["module", "action"],               // モジュール指定あり, アクション指定あり
            ["module", "action", "hoge"],       // モジュール指定あり, アクション指定あり, 深すぎる階層
            // オブジェクト
            $psr17Factory->createServerRequest("GET", "/"),                             // モジュール指定なし, アクション指定なし, パラメータなし
            $psr17Factory->createServerRequest("GET", "/?key=value"),                   // モジュール指定なし, アクション指定なし, パラメータあり
            $psr17Factory->createServerRequest("GET", "/action"),                       // モジュール指定なし, アクション指定あり, パラメータなし
            $psr17Factory->createServerRequest("GET", "/action?key=value"),             // モジュール指定なし, アクション指定あり, パラメータあり
            $psr17Factory->createServerRequest("GET", "/module/"),                      // モジュール指定あり, アクション指定なし, パラメータなし
            $psr17Factory->createServerRequest("GET", "/module/?key=value"),            // モジュール指定あり, アクション指定なし, パラメータあり
            $psr17Factory->createServerRequest("GET", "/module/action"),                // モジュール指定あり, アクション指定あり, パラメータなし
            $psr17Factory->createServerRequest("GET", "/module/action?key=value"),      // モジュール指定あり, アクション指定あり, パラメータあり
            $psr17Factory->createServerRequest("GET", "/module/action/hoge"),           // モジュール指定あり, アクション指定あり, パラメータなし, 深すぎる階層
            $psr17Factory->createServerRequest("GET", "/module/action/hoge?key=value"), // モジュール指定あり, アクション指定あり, パラメータあり, 深すぎる階層
        ];
    }
}
