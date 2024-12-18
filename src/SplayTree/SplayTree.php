<?php

declare(strict_types=1);

namespace Locr\Lib\SplayTree;

/**
 * @property-read ?Node $root
 * @property-read int $size
 * @implements \Iterator<int, ?Node>
 */
class SplayTree implements \Iterator
{
    /**
     * @var callable
     */
    private mixed $comparator;
    private ?Node $currentIteratorNode = null;
    private int $currentIteratorPosition = 0;
    private ?Node $root = null;
    private int $size = 0;

    public function __construct(?callable $comparator = null)
    {
        $this->comparator = $comparator ?? self::defaultComparator();
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'root' => $this->root,
            'size' => $this->size,
            default => null
        };
    }

    public function current(): mixed
    {
        return $this->currentIteratorNode;
    }

    public function key(): mixed
    {
        return $this->currentIteratorPosition;
    }

    public function next(): void
    {
        if (is_null($this->currentIteratorNode)) {
            return;
        }

        $this->currentIteratorNode = $this->nextNode($this->currentIteratorNode);
        $this->currentIteratorPosition++;
    }

    public function rewind(): void
    {
        $this->currentIteratorNode = $this->minNode();
        $this->currentIteratorPosition = 0;
    }

    public function valid(): bool
    {
        return !is_null($this->currentIteratorNode);
    }

    public function add(int|float $key, mixed $data = null): Node
    {
        $node = new Node($key, $data);

        if (is_null($this->root)) {
            $node->left = null;
            $node->right = null;
            $this->size++;
            $this->root = $node;
        }

        $comparator = $this->comparator;
        $t = self::splayInternal($key, $this->root ?? $node, $comparator);
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

        return $this->root ?? $node;
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
                if (!empty($q)) {
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
     * @param int[]|array<mixed>[] $keys
     * @param mixed[] $values
     */
    private static function createList(array $keys, array $values): ?Node
    {
        $head = new Node(null, null);
        $p = $head;
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $data = isset($values[$i]) ? $values[$i] : null;
            $p->next = new Node($key, $data);
            $p = $p->next;
        }
        $p->next = null;

        return $head->next;
    }

    private static function defaultComparator(): callable
    {
        return function (int|float $a, int|float $b): int {
            if ($a > $b) {
                return 1;
            } elseif ($a < $b) {
                return -1;
            }

            return 0;
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

    public function forEach(callable $visitor): self
    {
        $current = $this->root;
        $q = []; /* Initialize stack s */
        $done = false;

        while (!$done) {
            if (!is_null($current)) {
                $q[] = $current;
                $current = $current->left;
            } else {
                if (!empty($q)) {
                    $current = array_pop($q);
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
     * @param int|array<mixed> $key
     */
    public function insert(int|array $key, mixed $data = null): Node
    {
        $this->size++;
        $root = self::insertInternal($key, $data, $this->root, $this->comparator);
        $this->root = $root;
        return $root;
    }

    /**
     * @param int|array<mixed> $i
     */
    private static function insertInternal(int|array $i, mixed $data, ?Node $t, callable $comparator): Node
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
     * @return int[]|float[]|array<mixed>[]
     */
    public function keys(): array
    {
        $keys = [];
        $this->forEach(function (Node $node) use (&$keys) {
            if (!is_null($node->key)) {
                $keys[] = $node->key;
            }
        });

        return $keys;
    }

    /**
     * Bulk-load items. Both array have to be same size
     * @param int[]|array<mixed>[] $keys
     * @param array<mixed> $values
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
            $list = ['head' => $mergedList];
            $this->root = self::sortedListToBST($list, 0, $size);
        }

        return $this;
    }

    /**
     * @param int[]|array<mixed>[] $keys
     * @param mixed[] $values
     */
    private static function loadRecursive(array $keys, array $values, int $start, int $end): ?Node
    {
        $size = $end - $start;
        if ($size > 0) {
            $middle = (int)($start + floor($size / 2));
            $key = $keys[$middle];
            $data = $values[$middle] ?? null;
            $node = new Node($key, $data);
            $node->left = self::loadRecursive($keys, $values, $start, $middle);
            $node->right = self::loadRecursive($keys, $values, $middle + 1, $end);
            return $node;
        }
        return null;
    }

    /**
     * @return int|float|array<mixed>|null
     */
    public function max(): int|float|array|null
    {
        if (!is_null($this->root)) {
            $maxNode = $this->maxNode($this->root);
            if (!is_null($maxNode)) {
                return $maxNode->key;
            }
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

        if (is_null($left->key)) {
            return null;
        }
        $right = self::splayInternal($left->key, $right, $comparator);
        $right->left = $left;
        return $right;
    }

    private static function mergeLists(?Node $l1, ?Node $l2, callable $compare): ?Node
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

    /**
     * @return int|float|array<mixed>|null
     */
    public function min(): int|float|array|null
    {
        if (!is_null($this->root)) {
            $minNode = $this->minNode($this->root);
            if (!is_null($minNode)) {
                return $minNode->key;
            }
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

    public function nextNode(Node $d): ?Node
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
     * @return null|array{'key': int|float|array<mixed>, 'data': mixed}
     */
    public function pop(): ?array
    {
        if (!is_null($this->root)) {
            $node = $this->root;
            while ($node->left) {
                $node = $node->left;
            }
            if (is_null($node->key)) {
                return null;
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

    public function previousNode(Node $d): ?Node
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

    /**
     * @param callable(Node): string $printNode
     */
    private static function printRow(Node $root, string $prefix, bool $isTail, callable $out, callable $printNode): void
    {
        $tail = $isTail ? '└── ' : '├── ';
        $out("{$prefix}{$tail}{$printNode($root)}\n");
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
    public function range(int $low, int $high, callable $fn): self
    {
        $q = [];
        $compare = $this->comparator;
        $node = $this->root;
        $cmp = 0;

        while (!empty($q) || $node instanceof Node) {
            if ($node instanceof Node) {
                $q[] = $node;
                $node = $node->left;
            } elseif (!empty($q)) {
                $node = array_pop($q);
                $cmp = $compare($node->key, $high);
                if ($cmp > 0) {
                    break;
                } elseif ($compare($node->key, $low) >= 0) {
                    if ($fn($node)) {
                        return $this; // stop if something is returned
                    }
                }
                $node = $node->right;
            }
        }
        return $this;
    }

    public function remove(int|float $key): void
    {
        $this->root = $this->removeInternal($key, $this->root, $this->comparator);
    }

    /**
     * @param int|float|array<mixed> $i
     */
    private function removeInternal(int|float|array $i, ?Node $t, callable $comparator): ?Node
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

    /**
     * @param int[]|array<mixed>[] $keys
     * @param array<mixed> $values
     */
    private static function sort(array &$keys, array &$values, int $left, int $right, callable $compare): void
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

            if (!isset($values[$i])) {
                $values[$i] = null;
            }
            if (!isset($values[$j])) {
                $values[$j] = null;
            }
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
    private static function sortedListToBST(array &$list, int $start, int $end): ?Node
    {
        if (is_null($list['head'])) {
            return null;
        }

        $size = $end - $start;
        if ($size > 0) {
            $middle = (int)($start + floor($size / 2));
            $left = self::sortedListToBST($list, $start, $middle);

            $root = $list['head'];
            if (!is_null($root)) {
                $root->left = $left;

                $list['head'] = $root->next;

                $root->right = self::sortedListToBST($list, $middle + 1, $end);
            }

            return $root;
        }

        return null;
    }

    /**
     * @param int|array<mixed> $i
     */
    private static function splayInternal(int|float|array $i, Node $t, callable $comparator): Node
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
    public function split(int|float $key): array
    {
        return self::splitInternal($key, $this->root, $this->comparator);
    }

    /**
     * @return array{'left': ?Node, 'right': ?Node}
     */
    private static function splitInternal(int|float $key, ?Node $v, callable $comparator): array
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

    public function toList(): ?Node
    {
        return self::toListInternal($this->root);
    }

    private static function toListInternal(?Node $root): ?Node
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
                if (empty($q)) {
                    $done = true;
                    continue;
                }

                $next = array_pop($q);
                $p->next = $next;
                $p = $p->next;
                $current = $p;
                $current = $current->right;
            }
        }
        $p->next = null; // that'll work even if the tree was empty

        return $head->next;
    }

    /**
     * @param callable(Node): string $printNode
     */
    public function toString(?callable $printNode = null): string
    {
        $out = [];

        if (is_null($printNode)) {
            $printNode = function (Node $n): string {
                if (is_null($n->key)) {
                    return '';
                }
                if (is_array($n->key)) {
                    return implode(',', $n->key);
                }
                return (string)$n->key;
            };
        }

        if (!is_null($this->root)) {
            self::printRow(
                $this->root,
                '',
                true,
                function ($v) use (&$out) {
                    $out[] = $v;
                },
                $printNode
            );
        }

        return implode('', $out);
    }

    public function update(int $key, int $newKey, mixed $newData = null): void
    {
        $comparator = $this->comparator;
        list('left' => $left, 'right' => $right) = self::splitInternal($key, $this->root, $comparator);
        if ($comparator($key, $newKey) < 0) {
            $right = self::insertInternal($newKey, $newData, $right, $comparator);
        } else {
            $left = self::insertInternal($newKey, $newData, $left, $comparator);
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
