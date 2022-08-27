<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Error as PhpParserException;
use Uzbek\ClassTools\Exception\RuntimeException;

/**
 * Translate and print parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Writer
{
    /**
     * @var NodeTraverser Traverser used for altering parsed code
     */
    private readonly \PhpParser\NodeTraverser $nodeTraverser;

    /**
     * @var BracketingPrinter Printer used for printing traversed code
     */
    private readonly \Uzbek\ClassTools\Transformer\BracketingPrinter $bracketingPrinter;

    /**
     * Optionally inject dependencies
     *
     * Since Reader always makes definitions namespaced a PhpParser printer that
     * wraps the code in brackeded namespace statements must be used. The current
     * implementation of this is BracketingPrinter.
     */
    public function __construct(NodeTraverser $nodeTraverser = null, BracketingPrinter $bracketingPrinter = null)
    {
        $this->nodeTraverser = $nodeTraverser ?: new NodeTraverser();
        $this->bracketingPrinter = $bracketingPrinter ?: new BracketingPrinter();
    }

    /**
     * Apply translation to alter code
     */
    public function apply(NodeVisitor $nodeVisitor): self
    {
        $this->nodeTraverser->addVisitor($nodeVisitor);
        return $this;
    }

    /**
     * Generate new code snippet
     *
     * @throws RuntimeException If code generation failes
     */
    public function write(array $statements): string
    {
        try {
            return $this->bracketingPrinter->prettyPrint(
                $this->nodeTraverser->traverse(
                    $statements
                )
            );
        } catch (PhpParserException $phpParserException) {
            throw new RuntimeException(sprintf('Error generating code: %s', $phpParserException->getMessage()), 0, $phpParserException);
        }
    }
}
