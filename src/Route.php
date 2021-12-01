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
use Psr\Http\Message\ServerRequestInterface;

/**
 * このフレームワークではリクエストURLパスはモジュール名とアクション名の二階層で構成されます。
 * 
 * 通常、Psr\Http\Message\ServerRequestInterfaceの属性としてセットされ、アプリケーション内部で参照されます。
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
     * 空文字列、空配列はドキュメントルート(/)として解釈されます。
     *
     * @param string|array|ServerRequestInterface $requestPath
     * @return array
     */
    public static function parseRequestPath(string|array|ServerRequestInterface $requestPath): array
    {
        if ($requestPath instanceof ServerRequestInterface) {
            $requestPath = $requestPath->getUri()->getPath();
        }

        if (!$requestPath) {
            $requestPath = "/";
        }

        $pathNodes = $requestPath;

        if (is_string($pathNodes)) {
            $pathNodes = explode("/", explode("?", $pathNodes)[0]);
            array_shift($pathNodes);
        }

        if (count($pathNodes) == 1) {
            $pathNodes[1] = (isset($pathNodes[0]) && trim($pathNodes[0]) !== "" ? $pathNodes[0] : "index");
            $pathNodes[0] = "index";
        } else {
            $pathNodes[0] = (isset($pathNodes[0]) && trim($pathNodes[0]) !== "" ? $pathNodes[0] : "index");
            $pathNodes[1] = (isset($pathNodes[1]) && trim($pathNodes[1]) !== "" ? $pathNodes[1] : "index");
        }

        return [basename(trim($pathNodes[0])), basename(trim($pathNodes[1]))];
    }
}
