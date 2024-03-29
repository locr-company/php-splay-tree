<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeCompareTest extends TestCase
{
    /**
     * should function correctly given a non-reverse customCompare
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testCompare1(): void
    {
        $tree = new SplayTree(function ($a, $b) {
            return $b - $a;
        });
        $tree->insert(2);
        $tree->insert(1);
        $tree->insert(3);
        $this->assertEquals(3, $tree->size);
        $this->assertEquals(3, $tree->min());
        $this->assertEquals(1, $tree->max());
        $tree->remove(3);
        $this->assertEquals(2, $tree->size);
        $this->assertEquals(2, $tree->root->key);
        $this->assertNull($tree->root->left);
        $this->assertEquals(1, $tree->root->right->key);
    }

    /**
     * should support custom keys
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testCompare2(): void
    {
        $comparator = function (array $a, array $b) {
            return $a['value'] - $b['value'];
        };
        $tree = new SplayTree($comparator);
        $objects = [];
        for ($i = 0; $i < 10; $i++) {
            $objects[] = [
                'value' => $i,
                'data' => pow($i, 2)
            ];
        }
        // this use of the shuffle function has not a cryptographic purpose!
        shuffle($objects); // Devskim: ignore DS148264

        foreach ($objects as $o) {
            $tree->insert($o);
        }

        $slicedObjects = array_slice($objects, 0);
        usort($slicedObjects, $comparator);

        $this->assertEquals(
            array_map(function ($k) {
                return $k['value'];
            }, $slicedObjects),
            array_map(function ($k) {
                return $k['value'];
            }, $tree->keys())
        );
    }
}
