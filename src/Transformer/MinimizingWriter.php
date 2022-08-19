<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace Uzbek\ClassTools\Transformer;

use PhpParser\NodeTraverser;
use Uzbek\ClassTools\Transformer\Action\CommentStripper;
use Uzbek\ClassTools\Transformer\Action\NodeStripper;
use Uzbek\ClassTools\Transformer\Action\NameResolver;

/**
 * Minimize parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class MinimizingWriter extends Writer
{
    public function __construct(NodeTraverser $traverser = null)
    {
        parent::__construct($traverser);
        $this->apply(new CommentStripper);
        $this->apply(new NameResolver);
        $this->apply(new NodeStripper('Stmt_Use'));
    }
}
