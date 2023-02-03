# Fast splay tree

[Splay-tree](https://en.wikipedia.org/wiki/Splay_tree): **fast**(non-recursive) and **simple**(< 1000 lines of code)
Implementation is adapted directly from this [GitHub Repository](https://github.com/w8r/splay-tree/).


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

## Develop

```shell
composer install
```
