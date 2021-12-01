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

namespace Kdevy\Phpfw3Lib\Interfaces;

interface RouteInterface
{
    /**
     * @return string
     */
    public function getModuleName(): string;

    /**
     * @return string
     */
    public function getActionName(): string;

    /**
     * @return array
     */
    public function getPath(): array;

    /**
     * @return string
     */
    public function getPathName(): string;
}
