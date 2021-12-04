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

namespace Kdevy\Phpfw3Lib\Utils;

class StringUtil
{
    /**
     * インスタンス化を禁止
     */
    private function __construct()
    {
    }

    /**
     * 文字列に含まれる名前付きプレースホルダ "%{placeholder}" を指定のマップに置き換えます。
     *
     * @param string $str
     * @param array<int,array<key,string>> ...$replacements
     * @return string
     */
    static public function format(string $str, ...$replacements): string
    {
        array_walk($replacements, function ($replacement) use (&$str) {
            $replacement = array_combine(
                array_map(fn ($rep_key) => '%{' . $rep_key . '}', array_keys($replacement)),
                array_values($replacement)
            );

            $str = strtr($str, $replacement);
        });

        return $str;
    }
}
