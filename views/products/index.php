<?php

use App\Entities\Product;
use PhpFramework\View\View;

/**
 * @var Product[] $products
 */

View::extends('application');
?>

<h3>I am a list of products</h3>
<ul>
    <?php foreach ($products as $product) : ?>
        <li>
            <p>
                <?= $product->name;?>: <a href="/product/<?= $product->id;?>">#<?= $product->id;?></a>
            </p>
            <p><?= $product->description;?></p>
        </li>
    <?php endforeach;?>
</ul>
