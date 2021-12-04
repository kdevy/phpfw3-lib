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

use Kdevy\Phpfw3Lib\Utils\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testFormat(): void
    {
        $excepted = "Plato's dialogues are among the most comprehensive accounts of Socrates to survive from antiquity. 
        They demonstrate the Socratic approach to areas of philosophy including rationalism and ethics.";

        $text = "%{ph1} are among the most comprehensive accounts of %{ph2} to survive from antiquity. 
        They demonstrate the Socratic approach to areas of %{ph3} including %{ph4} and ethics.";

        $replacements1 = ["ph1" => "Plato's dialogues", "ph2" => "Socrates", "ph3" => "philosophy", "ph4" => "rationalism"];
        $replacements2 = ["ph1" => "Plato's dialogues", "ph2" => "Socrates"];
        $replacements3 = ["ph3" => "philosophy", "ph4" => "rationalism"];

        $this->assertSame(
            $excepted,
            StringUtil::format($text, $replacements1)
        );
        $this->assertSame(
            $excepted,
            StringUtil::format($text, $replacements2, $replacements3)
        );
    }
}
