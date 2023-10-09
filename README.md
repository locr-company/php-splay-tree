# Fast splay tree

[Splay-tree](https://en.wikipedia.org/wiki/Splay_tree): **fast**(non-recursive) and **simple**(< 1000 lines of code)
Implementation is adapted directly from this [GitHub Repository](https://github.com/w8r/splay-tree/).

![php](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)
[![codecov](https://codecov.io/gh/locr-company/php-splay-tree/branch/main/graph/badge.svg?token=KESLR0XLJJ)](https://codecov.io/gh/locr-company/php-splay-tree)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=locr-company_php-splay-tree&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=locr-company_php-splay-tree)


This tree is based on **top-down** splaying algorithm by D.Sleator. It supports
 - splitting, merging
 - updating of the keys
 - bulk loading of the items into an empty or non-empty tree
 - insertion with duplicates or no duplicates
 - lookup without splaying

![Splay-tree](https://i.stack.imgur.com/CNSAZ.png)

| Operation     | Average       | Worst case             |
| ------------- | ------------- | ---------------------- |
| Space         | **O(n)**      | **O(n)**               |
| Search        | **O(log n)**  | **amortized O(log n)** |
| Insert        | **O(log n)**  | **amortized O(log n)** |
| Delete        | **O(log n)**  | **amortized O(log n)** |


## Install

```shell
composer require locr-company/splay-tree
```

```php
<?php

use Locr\Lib\SplayTree\SplayTree;

$tree = new SplayTree();
```

## API

* `new SplayTree(callable $comparator = null)`, where `$comparator` is optional comparison function
* `$tree->insert(int|array $key, mixed $data = null): Node` - Insert item, allow duplicate keys
* `$tree->add(int|float $key, mixed $data = null): Node` - Insert item if it is not present
* `$tree->remove(int|float $key): void` - Remove item
* `$tree->find(int $key): ?Node` - Return node by its key
* `$tree->findStatic(int $key): ?Node` - Return node by its key (doesn't re-balance the tree)
* `$tree->at(int $index): ?Node` - Return node by its index in sorted order of keys
* `$tree->contains(int $key): bool` - Whether a node with the given key is in the tree
* `$tree->forEach(callable $visitor): self` In-order traversal
* `$tree->keys(): array` - Returns the array of keys in order
* `$tree->values(): array` - Returns the array of data fields in order
* `$tree->range(int $low, int $high, callable $fn): self` - Walks the range of keys in order. Stops, if the visitor function returns a non-zero value.
* `$tree->pop(): array` - Removes smallest node
* `$tree->min(): int|float|array|null` - Returns min key
* `$tree->max(): int|float|array|null` - Returns max key
* `$tree->minNode(?Node $t = null): ?Node` - Returns the node with smallest key
* `$tree->maxNode(?Node $t = null): ?Node` - Returns the node with highest key
* `$tree->previousNode(Node $d): ?Node` - Predecessor node
* `$tree->nextNode(Node $d): ?Node` - Successor node
* `$tree->load(array $keys, array $values = [], bool $presort = false): self` - Bulk-load items. It expects values and keys to be sorted, but if `presort` is `true`, it will sort keys and values using the comparator(in-place, your arrays are going to be altered).

**Comparator**

`function(int|float $a, int|float $b): int` - Comparator function between two keys, it returns
 * `0` if the keys are equal
 * `<0` if `a < b`
 * `>0` if `a > b`

 The comparator function is extremely important, in case of errors you might end
 up with a wrongly constructed tree or would not be able to retrieve your items.
 It is crucial to test the return values of your `comparator(a, b)` and `comparator(b, a)`
 to make sure it's working correctly, otherwise you may have bugs that are very
 unpredictable and hard to catch.

 **Duplicate keys**

* `insert()` method allows duplicate keys. This can be useful in certain applications (example: overlapping
 points in 2D).
* `add()` method will not allow duplicate keys - if key is already present in the tree, no new node is created

## Example

```php
<?php

use Locr\Lib\SplayTree\SplayTree;

$t = new SplayTree();
$t->insert(5);
$t->insert(-10);
$t->insert(0);
$t->insert(33);
$t->insert(2);

print_r($t->keys());
/**
 * Array(
 *  [0] => -10  
 *  [1] => 0  
 *  [2] => 2  
 *  [3] => 5  
 *  [4] => 33
 * )
 */
print $t->size;  // 5
print $t->min(); // -10
print $t->max(); // 33

$t->remove(0);
print $t->size;   // 4
```

**Custom comparator (reverse sort)**

```php
<?php

use Locr\Lib\SplayTree\SplayTree;

$t = new SplayTree(function ($a, $b) {
    return $b - $a;
});
$t->insert(5);
$t->insert(-10);
$t->insert(0);
$t->insert(33);
$t->insert(2);

print_r($t->keys());
/**
 * Array
 *(
 *  [0] => 33
 *  [1] => 5
 *  [2] => 2
 *  [3] => 0
 *  [4] => -10
 *)
 */
```

**Bulk insert**

```php
<?php

use Locr\Lib\SplayTree\SplayTree;

$t = new SplayTree();
$t->load([3, 2, -10, 20], ['C', 'B', 'A', 'D']);
print_r($t->keys());
/**
 * Array
 *(
 *  [0] => 3
 *  [1] => 2
 *  [2] => -10
 *  [3] => 20
 *)
 */
print_r($t->values());
/**
 * Array
 *(
 *  [0] => C
 *  [1] => B
 *  [2] => A
 *  [3] => D
 *)
 */
```

## Develop

```shell
composer install
```
