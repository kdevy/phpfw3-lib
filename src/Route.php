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

namespace Kdevy\Phpfw3Lib;

use Kdevy\Phpfw3Lib\Interfaces\RouteInterface;
use Kdevy\Phpfw3Lib\Exceptions\RouteParseError;
use Psr\Http\Message\ServerRequestInterface;

/**
 * このフレームワークでのルートとは、モジュール名とアクション名の二階層で構成されるリクエストパスを意味します。
 */
class Route implements RouteInterface
{
    /**
     * @var string
     */
    private string $moduleName;

    /**
     * @var string
     */
    private string $actionName;

    /**
     * @param string|array|ServerRequestInterface $requestPath
     */
    public function __construct(string|array|ServerRequestInterface $requestPath)
    {
        list($this->moduleName, $this->actionName) = static::parseRequestPath($requestPath);
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @return array
     */
    public function getPath(): array
    {
        return [$this->moduleName, $this->actionName];
    }

    /**
     * @return string
     */
    public function getPathName(): string
    {
        return "/{$this->moduleName}/{$this->actionName}";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getPathName();
    }

    /**
     * リクエストパスをモジュール名とアクション名に分解します。
     *
     * 空文字列、空配列はドキュメントルート(/)として解釈されます。
     * ドキュメントルートのモジュール名、アクション名は"index"になります。また、アクション名の指定が無い場合は"index"になります。
     * 三階層目以降("hoge"以降の文字列)のパスは無視されます。(/module/action/hoge/..)
     * 
     * モジュール名またはアクション名に使用できない文字が含まれる場合は RouteParseError の例外が発生します。
     * 三階層目以降に使用できない文字が含まれている場合は例外は発生しません。
     * モジュール名とアクション名は大文字と小文字を区別しません。
     *
     * @param string|array<int,string>|ServerRequestInterface $requestPath
     * @return array<int,string> 一要素目: モジュール名、二要素目： アクション名
     * @throws RouteParseError
     */
    public static function parseRequestPath(string|array|ServerRequestInterface $requestPath): array
    {
        $result = ["", ""];

        if ($requestPath instanceof ServerRequestInterface) {
            $requestPath = $requestPath->getUri()->getPath();
        }

        $requestPath = ($requestPath ? $requestPath : "/");

        $pathNodes = $requestPath;

        if (is_string($pathNodes)) {
            $pathNodes = explode("/", explode("?", $pathNodes)[0]);
            array_shift($pathNodes);
        }

        $pathNodes = array_map("self::formatName", array_slice($pathNodes, 0, 2));

        if (count($pathNodes) == 1) {
            $result[1] = (isset($pathNodes[0]) && $pathNodes[0] !== "" ? $pathNodes[0] : "index");
            $result[0] = "index";
        } else {
            $result[0] = (isset($pathNodes[0]) && $pathNodes[0] !== "" ? $pathNodes[0] : "index");
            $result[1] = (isset($pathNodes[1]) && $pathNodes[1] !== "" ? $pathNodes[1] : "index");
        }

        if (!self::isValidModuleName($result[0])) {
            throw new RouteParseError("Invalid for the module name '{$result[0]}'.");
        }
        if (!self::isValidActionName($result[1])) {
            throw new RouteParseError("Invalid for the action name '{$result[1]}'.");
        }

        return $result;
    }

    /**
     * @param string $module_or_action_name
     * @return string
     */
    static public function formatName(string $module_or_action_name): string
    {
        return basename(trim(strtolower($module_or_action_name)));
    }

    /**
     * モジュール名として使用できる文字列は、任意のアルファベット/数字/アンダースコア/ハイフンの文字列です。
     *
     * @param string $module_name
     * @return boolean
     */
    static public function isValidModuleName(string $module_name): bool
    {
        return preg_match('/^[a-zA-Z0-9_\-]*$/', $module_name) === 1;
    }

    /**
     * アクション名として使用できる文字列は、先頭がアルファベットあるいはアンダースコアで始まり、その後に任意の数のアルファベット/数字/アンダースコアが続くものです。
     * 
     * @param string $action_name
     * @return boolean
     */
    static public function isValidActionName(string $action_name): bool
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $action_name) === 1;
    }
}
