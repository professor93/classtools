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
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Name;

/**
 * Wrap code in namespace
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NamespaceWrapper extends NodeVisitorAbstract
{
    /**
     * Wrap code in namespace
     */
    public function __construct(
        /**
         * @var string Name of namespace
         */
        private readonly string $namespaceName
    ) {
    }

    /**
     * {inheritdoc}
     *
     * @return Namespace_[]
     */
    public function beforeTraverse(array $nodes): array
    {
        // Merge if code is namespaced
        if (isset($nodes[0]) && $nodes[0] instanceof Namespace_) {
            if ($this->namespaceName !== '' && $this->namespaceName !== '0') {
                if ((string)$nodes[0]->name == '') {
                    $nodes[0]->name = new Name($this->namespaceName);
                } else {
                    $nodes[0]->name = Name::concat($this->namespaceName, $nodes[0]->name);
                }
            }

            return $nodes;
        }

        // Else create new node
        return [
            new Namespace_(
                new Name($this->namespaceName),
                $nodes
            )
        ];
    }
}
