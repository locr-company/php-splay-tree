<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreePrintTest extends TestCase
{
    /**
     * should print the tree
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testPrint1(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 3; $i++) {
            $tree->insert($i);
        }

        $tree->find(2);
        $expectedString = "└── 2\n";
        $expectedString .= "    ├── 1\n";
        $expectedString .= "    │   ├── 0\n";
        $this->assertEquals($expectedString, $tree->toString());
    }

    /**
     * should print the balanced tree
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testPrint2(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 3; $i++) {
            $tree->insert($i);
        }

        $tree->find(1);
        $expectedString = "└── 1\n";
        $expectedString .= "    ├── 0\n";
        $expectedString .= "    └── 2\n";
        $this->assertEquals($expectedString, $tree->toString());
    }
}
