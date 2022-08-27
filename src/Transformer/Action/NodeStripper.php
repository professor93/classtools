<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer\Action;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\NodeTraverser;

/**
 * Strip nodes of registered type
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NodeStripper extends NodeVisitorAbstract
{
    /**
     * Register what nodes to strip using a fully quilified PhpParser class name
     *
     * @param string $nodeType Node type (see Node::getType())
     */
    public function __construct(private readonly string $nodeType)
    {
    }

    public function leaveNode(Node $node)
    {
        if ($node->getType() === $this->nodeType) {
            return NodeTraverser::REMOVE_NODE;
        }

        return null;
    }
}
