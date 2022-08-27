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

use Uzbek\ClassTools\Name;
use Uzbek\ClassTools\Exception\RuntimeException;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;

/**
 * Search namespaces for definied classes
 *
 * Crawl namespaces for classes that were wrongly put in namespace by NameResolver
 * (if code is moved to the namespace before parsing takes place).
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NamespaceCrawler extends NodeVisitorAbstract
{
    /**
     * @var Name[] List of namespaces to test for each undefined name
     */
    private array $search = [];

    /**
     * @var Name[] List of namespaces that will be allowed even if not defined
     */
    private array $whitelist = [];

    /**
     * Search namespaces for definied classes
     *
     * @param string[] $search    List of namespaces to test for each undefined name
     * @param string[] $whitelist namespaces that will be allowed even if not defined
     */
    public function __construct(array $search, array $whitelist = [])
    {
        foreach ($search as $namespace) {
            $this->search[] = new Name($namespace);
        }

        foreach ($whitelist as $namespace) {
            $this->whitelist[] = new Name($namespace);
        }
    }

    /**
     * Resolve unexisting names by searching specified namespaces
     *
     * @throws RuntimeException If name can not be resolved
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof FullyQualified) {
            $name = new Name((string)$node);
            $whitelisted = $this->isWhitelisted($name);
            if (!$name->isDefined(!$whitelisted)) {
                /** @var Name $namespace */
                foreach ($this->search as $namespace) {
                    $newName = new Name(sprintf('%s\%s', $namespace, $node->getLast()));
                    if ($newName->isDefined()) {
                        return $newName->createNode();
                    }
                }

                if (!$whitelisted) {
                    throw new RuntimeException(sprintf('Unable to resolve class <%s>.', $node));
                }
            }
        }
    }

    /**
     * Check if name is whitelisted
     */
    public function isWhitelisted(Name $name): bool
    {
        /** @var Name $namespace */
        foreach ($this->whitelist as $namespace) {
            if ($name->inNamespace($namespace)) {
                return true;
            }
        }

        return false;
    }
}
