<?php

declare(strict_types=1);

namespace SplayTree;

/**
 * @property-read ?Node $root
 * @property-read int $size
 */
class SplayTree
{
    /**
     * @var callable
     */
    private mixed $comparator;
    private ?Node $root = null;
    private int $size = 0;

    public function __construct(callable $comparator = null)
    {
        $this->comparator = $comparator ?? self::defaultComparator();
    }

    public function __get(string $name): mixed
    {
        switch ($name) {
            case 'root':
                return $this->root;
            case 'size':
                return $this->size;
        }

        return null;
    }

    public function add(int $key, mixed $data = null): Node
    {
        $node = new Node($key, $data);

        if (is_null($this->root)) {
            $node->left = null;
            $node->right = null;
            $this->size++;
            $this->root = $node;
        }

        $comparator = $this->comparator;
        $t = self::splayInternal($key, $this->root, $comparator);
        $cmp = $comparator($key, $t->key);
        if ($cmp === 0) {
            $this->root = $t;
        } else {
            if ($cmp < 0) {
                $node->left = $t->left;
                $node->right = $t;
                $t->left = null;
            } elseif ($cmp > 0) {
                $node->right = $t->right;
                $node->left = $t;
                $t->right = null;
            }
            $this->size++;
            $this->root = $node;
        }

        return $this->root;
    }

    public function at(int $index): ?Node
    {
        $current = $this->root;
        $done = false;
        $i = 0;
        $q = [];

        while (!$done) {
            if (!is_null($current)) {
                $q[] = $current;
                $current = $current->left;
            } else {
                if (count($q) > 0) {
                    $current = array_pop($q);
                    if ($i === $index) {
                        return $current;
                    }
                    $i++;
                    $current = $current->right;
                } else {
                    $done = true;
                }
            }
        }

        return null;
    }

    public function clear(): self
    {
        $this->root = null;
        $this->size = 0;
        return $this;
    }

    public function contains(int $key): bool
    {
        $current = $this->root;
        $compare = $this->comparator;
        while ($current) {
            $cmp = $compare($key, $current->key);
            if ($cmp === 0) {
                return true;
            } elseif ($cmp < 0) {
                $current = $current->left;
            } else {
                $current = $current->right;
            }
        }

        return false;
    }

    /**
     * @param int[] $keys
     * @param mixed[] $values
     */
    private static function createList(array $keys, array $values): Node
    {
        $head = new Node(null, null);
        $p = $head;
        for ($i = 0; $i < count($keys); $i++) {
            $p = $p->next = new Node($keys[$i], $values[$i]);
        }
        $p->next = null;

        return $head->next;
    }

    private static function defaultComparator(): callable
    {
        return function (int $a, int $b): int {
            return $a > $b ? 1 : ($a < $b ? -1 : 0);
        };
    }

    public function find(int $key): ?Node
    {
        if ($this->root) {
            $this->root = self::splayInternal($key, $this->root, $this->comparator);
            $comparator = $this->comparator;
            if ($comparator($key, $this->root->key) !== 0) {
                return null;
            }
        }

        return $this->root;
    }

    public function findStatic(int $key): ?Node
    {
        $current = $this->root;
        $compare = $this->comparator;
        while (!is_null($current)) {
            $cmp = $compare($key, $current->key);
            if ($cmp === 0) {
                return $current;
            } elseif ($cmp < 0) {
                $current = $current->left;
            } else {
                $current = $current->right;
            }
        }

        return null;
    }

    public function forEach(callable $visitor, mixed $ctx = null): self
    {
        $current = $this->root;
        $q = []; /* Initialize stack s */
        $done = false;

        while (!$done) {
            if (!is_null($current)) {
                $q[] = $current;
                $current = $current->left;
            } else {
                if (count($q) !== 0) {
                    $current = array_pop($q);
                    // $visitor.call($ctx, $current);
                    $visitor($current);

                    $current = $current->right;
                } else {
                    $done = true;
                }
            }
        }

        return $this;
    }

    /**
     * Inserts a key, allows duplicates
     */
    public function insert(int $key, mixed $data = null): Node
    {
        $this->size++;
        $this->root = self::insertInternal($key, $data, $this->root, $this->comparator);
        return $this->root;
    }

    private static function insertInternal(int $i, mixed $data, Node $t, callable $comparator): Node
    {
        $node = new Node($i, $data);

        if (is_null($t)) {
            $node->left = null;
            $node->right = null;
            return $node;
        }

        $t = self::splayInternal($i, $t, $comparator);
        $cmp = $comparator($i, $t->key);
        if ($cmp < 0) {
            $node->left = $t->left;
            $node->right = $t;
            $t->left = null;
        } elseif ($cmp >= 0) {
            $node->right = $t->right;
            $node->left = $t;
            $t->right = null;
        }

        return $node;
    }

    public function isEmpty(): bool
    {
        return is_null($this->root);
    }

    /**
     * Returns array of keys
     * @return int[]
     */
    public function keys(): array
    {
        $keys = [];
        $this->forEach(function (Node $node) use (&$keys) {
            $keys[] = $node->key;
        });
        return $keys;
    }

    /**
     * Bulk-load items. Both array have to be same size
     */
    public function load(array $keys, array $values = [], bool $presort = false): self
    {
        $size = count($keys);
        $comparator = $this->comparator;

        // sort if needed
        if ($presort) {
            self::sort($keys, $values, 0, $size - 1, $comparator);
        }

        if (is_null($this->root)) { // empty tree
            $this->root = self::loadRecursive($keys, $values, 0, $size);
            $this->size = $size;
        } else { // that re-builds the whole tree from two in-order traversals
            $mergedList = self::mergeLists($this->toList(), self::createList($keys, $values), $comparator);
            $size = $this->size + $size;
            $this->root = self::sortedListToBST(['head' => $mergedList], 0, $size);
        }

        return $this;
    }

    /**
     * @param int[] $keys
     * @param mixed[] $values
     */
    private static function loadRecursive(array $keys, array $values, int $start, int $end): ?Node
    {
        $size = $end - $start;
        if ($size > 0) {
            $middle = $start + floor($size / 2);
            $key = $keys[$middle];
            $data = $values[$middle];
            $node = new Node($key, $data);
            $node->left = self::loadRecursive($keys, $values, $start, $middle);
            $node->right = self::loadRecursive($keys, $values, $middle + 1, $end);
            return $node;
        }
        return null;
    }

    public function max(): ?int
    {
        if (!is_null($this->root)) {
            return $this->maxNode($this->root)->key;
        }

        return null;
    }

    public function maxNode(?Node $t = null): ?Node
    {
        $t = $t ?? $this->root;
        if (!is_null($t)) {
            while (!is_null($t->right)) {
                $t = $t->right;
            }
        }

        return $t;
    }

    private static function merge(?Node $left, ?Node $right, callable $comparator): ?Node
    {
        if (is_null($right)) {
            return $left;
        }
        if (is_null($left)) {
            return $right;
        }

        $right = self::splayInternal($left->key, $right, $comparator);
        $right->left = $left;
        return $right;
    }

    private static function mergeLists(Node $l1, Node $l2, callable $compare): Node
    {
        $head = new Node(null, null); // dummy
        $p = $head;

        $p1 = $l1;
        $p2 = $l2;

        while (!is_null($p1) && !is_null($p2)) {
            if ($compare($p1->key, $p2->key) < 0) {
                $p->next = $p1;
                $p1 = $p1->next;
            } else {
                $p->next = $p2;
                $p2 = $p2->next;
            }
            $p = $p->next;
        }

        if (!is_null($p1)) {
            $p->next = $p1;
        } elseif (!is_null($p2)) {
            $p->next = $p2;
        }

        return $head->next;
    }

    public function min(): ?int
    {
        if (!is_null($this->root)) {
            return $this->minNode($this->root)->key;
        }

        return null;
    }

    public function minNode(?Node $t = null): ?Node
    {
        $t = $t ?? $this->root;
        if (!is_null($t)) {
            while (!is_null($t->left)) {
                $t = $t->left;
            }
        }

        return $t;
    }

    public function next(Node $d): ?Node
    {
        $root = $this->root;
        $successor = null;

        if ($d->right) {
            $successor = $d->right;
            while (!is_null($successor->left)) {
                $successor = $successor->left;
            }
            return $successor;
        }

        $comparator = $this->comparator;
        while (!is_null($root)) {
            $cmp = $comparator($d->key, $root->key);
            if ($cmp === 0) {
                break;
            } elseif ($cmp < 0) {
                $successor = $root;
                $root = $root->left;
            } else {
                $root = $root->right;
            }
        }

        return $successor;
    }

    /**
     * Removes and returns the node with smallest key
     * @return null|array{'key': int, 'data': mixed}
     */
    public function pop(): ?array
    {
        $node = $this->root;
        if (!is_null($node)) {
            while ($node->left) {
                $node = $node->left;
            }
            $this->root = self::splayInternal($node->key, $this->root, $this->comparator);
            $this->root = $this->removeInternal($node->key, $this->root, $this->comparator);
            return [
                'key' => $node->key,
                'data' => $node->data
            ];
        }

        return null;
    }

    public function prev(Node $d): ?Node
    {
        $root = $this->root;
        $predecessor = null;

        if (!is_null($d->left)) {
            $predecessor = $d->left;
            while (!is_null($predecessor->right)) {
                $predecessor = $predecessor->right;
            }
            return $predecessor;
        }

        $comparator = $this->comparator;
        while (!is_null($root)) {
            $cmp = $comparator($d->key, $root->key);
            if ($cmp === 0) {
                break;
            } elseif ($cmp < 0) {
                $root = $root->left;
            } else {
                $predecessor = $root;
                $root = $root->right;
            }
        }
        return $predecessor;
    }

    private static function printRow(Node $root, string $prefix, bool $isTail, callable $out, callable $printNode): void
    {
        //out(`${ prefix }${ isTail ? '└── ' : '├── ' }${ printNode(root) }\n`);
        $indent = $prefix . ($isTail ? '    ' : '│   ');
        if (!is_null($root->left)) {
            self::printRow($root->left, $indent, false, $out, $printNode);
        }
        if (!is_null($root->right)) {
            self::printRow($root->right, $indent, true, $out, $printNode);
        }
    }

    /**
     * Walk key range from `low` to `high`. Stops if `fn` returns a value.
     */
    public function range(int $low, int $high, callable $fn, mixed $ctx = null): self
    {
        $q = [];
        $compare = $this->comparator;
        $node = $this->root;
        $cmp = 0;

        while (count($q) !== 0 || !is_null($node)) {
            if (!is_null($node)) {
                $q[] = $node;
                $node = $node->left;
            } else {
                $node = array_pop($q);
                $cmp = $compare($node->key, $high);
                if ($cmp > 0) {
                    break;
                } elseif ($compare($node->key, $low) >= 0) {
                    // if ($fn.call(ctx, node)) {
                    if ($fn($node)) {
                        return $this; // stop if something is returned
                    }
                }
                $node = $node->right;
            }
        }
        return $this;
    }

    public function remove(int $key): void
    {
        $this->root = $this->removeInternal($key, $this->root, $this->comparator);
    }

    private function removeInternal(int $i, ?Node $t, callable $comparator): ?Node
    {
        $x = null;
        if (is_null($t)) {
            return null;
        }
        $t = self::splayInternal($i, $t, $comparator);
        $cmp = $comparator($i, $t->key);
        if ($cmp === 0) { /* found it */
            if (is_null($t->left)) {
                $x = $t->right;
            } else {
                $x = self::splayInternal($i, $t->left, $comparator);
                $x->right = $t->right;
            }
            $this->size--;
            return $x;
        }

        return $t; /* It wasn't there */
    }

    private static function sort(array $keys, array $values, int $left, int $right, callable $compare): void
    {
        if ($left >= $right) {
            return;
        }

        $pivot = $keys[($left + $right) >> 1];
        $i = $left - 1;
        $j = $right + 1;

        while (true) {
            do {
                $i++;
            } while ($compare($keys[$i], $pivot) < 0);
            do {
                $j--;
            } while ($compare($keys[$j], $pivot) > 0);
            if ($i >= $j) {
                break;
            }

            $tmp = $keys[$i];
            $keys[$i] = $keys[$j];
            $keys[$j] = $tmp;

            $tmp = $values[$i];
            $values[$i] = $values[$j];
            $values[$j] = $tmp;
        }

        self::sort($keys, $values, $left, $j, $compare);
        self::sort($keys, $values, $j + 1, $right, $compare);
    }

    /**
     * @param array{'head': Node|null} $list
     */
    private static function sortedListToBST(array $list, int $start, int $end): ?Node
    {
        $size = $end - $start;
        if ($size > 0) {
            $middle = $start + floor($size / 2);
            $left = self::sortedListToBST($list, $start, $middle);

            $root = $list['head'];
            $root->left = $left;

            $list['head'] = $list['head']->next;

            $root->right = self::sortedListToBST($list, $middle + 1, $end);

            return $root;
        }

        return null;
    }

    private static function splayInternal(int $i, ?Node $t, callable $comparator): Node
    {
        $n = new Node(null, null);
        $l = $n;
        $r = $n;

        while (true) {
            $cmp = $comparator($i, $t->key);
            if ($cmp < 0) {
                if (is_null($t->left)) {
                    break;
                }
                if ($comparator($i, $t->left->key) < 0) {
                    $y = $t->left;                  /* rotate right */
                    $t->left = $y->right;
                    $y->right = $t;
                    $t = $y;
                    if (is_null($t->left)) {
                        break;
                    }
                }
                $r->left = $t;                      /* link right */
                $r = $t;
                $t = $t->left;
            } elseif ($cmp > 0) {
                if (is_null($t->right)) {
                    break;
                }
                if ($comparator($i, $t->right->key) > 0) {
                    $y = $t->right;                 /* rotate left */
                    $t->right = $y->left;
                    $y->left = $t;
                    $t = $y;
                    if (is_null($t->right)) {
                        break;
                    }
                }
                $l->right = $t;                     /* link left */
                $l = $t;
                $t = $t->right;
            } else {
                break;
            }
        }
        /* assemble */
        $l->right = $t->left;
        $r->left = $t->right;
        $t->left = $n->right;
        $t->right = $n->left;
        return $t;
    }

    /**
     * @return array{'left': ?Node, 'right': ?Node}
     */
    public function split(int $key): array
    {
        return self::splitInternal($key, $this->root, $this->comparator);
    }

    /**
     * @return array{'left': ?Node, 'right': ?Node}
     */
    private static function splitInternal(int $key, Node $v, callable $comparator): array
    {
        $left = null;
        $right = null;
        if (!is_null($v)) {
            $v = self::splayInternal($key, $v, $comparator);

            $cmp = $comparator($v->key, $key);
            if ($cmp === 0) {
                $left  = $v->left;
                $right = $v->right;
            } elseif ($cmp < 0) {
                $right = $v->right;
                $v->right = null;
                $left = $v;
            } else {
                $left = $v->left;
                $v->left = null;
                $right  = $v;
            }
        }

        return [
            'left' => $left,
            'right' => $right
        ];
    }

    public function toList()
    {
        return self::toListInternal($this->root);
    }

    private static function toListInternal(Node $root): Node
    {
        $current = $root;
        $q = [];
        $done = false;

        $head = new Node(null, null);
        $p = $head;

        while (!$done) {
            if (!is_null($current)) {
                $q[] = $current;
                $current = $current->left;
            } else {
                if (count($q) > 0) {
                    $p->next = array_pop($q);
                    $p = $p->next;
                    $current = $p;
                    $current = $current->right;
                } else {
                    $done = true;
                }
            }
        }
        $p->next = null; // that'll work even if the tree was empty

        return $head->next;
    }

    public function toString(callable $printNode): string
    {
        $out = [];
        self::printRow(
            $this->root,
            '',
            true,
            function ($v) use (&$out) {
                $out[] = $v;
            },
            $printNode
        );
        return implode('', $out);
    }

    public function update(int $key, int $newKey, mixed $newData = null): void
    {
        $comparator = $this->comparator;
        list('left' => $left, 'right' => $right) = self::split($key, $this->root, $comparator);
        if ($comparator($key, $newKey) < 0) {
            $right = self::insert($newKey, $newData, $right, $comparator);
        } else {
            $left = self::insert($newKey, $newData, $left, $comparator);
        }
        $this->root = self::merge($left, $right, $comparator);
    }

    /**
     * Returns array of all the data in the nodes
     * @return mixed[]
     */
    public function values(): array
    {
        $values = [];
        $this->forEach(function (Node $node) use (&$values) {
            $values[] = $node->data;
        });
        return $values;
    }
}
