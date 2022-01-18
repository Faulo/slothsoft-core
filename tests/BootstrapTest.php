<?php
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase {

    public function testNormalizeSlashes() {
        $input = '/A\\B';

        $this->assertEquals(DIRECTORY_SEPARATOR . 'A' . DIRECTORY_SEPARATOR . 'B', normalize_slashes($input));
    }
}

