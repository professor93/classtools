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

use Uzbek\ClassTools\Exception\RuntimeException;
use Uzbek\ClassTools\Exception\ReaderException;
use Uzbek\ClassTools\Name;
use PhpParser\Lexer\Emulative;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * Read classes, interfaces and traits from php snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class Reader
{
    /**
     * @var Namespace_[] Collection of definitions in snippet
     */
    private array $defs = [];

    /**
     * @var string[] Case sensitive definition names
     */
    private array $names = [];

    /**
     * @var \PhpParser\Node[]|null The global statement object
     */
    private ?array $global = [];

    /**
     * Optionally inject parser
     *
     * @throws ReaderException If snippet contains a syntax error
     */
    public function __construct(string $snippet, Parser $parser = null)
    {
        if (is_null($parser)) {
            $parserFactory = new ParserFactory();
            $parser = $parserFactory->create(ParserFactory::PREFER_PHP5);
        }

        try {
            $this->global = $parser->parse($snippet);
        } catch (\PhpParser\Error $error) {
            throw new ReaderException($error->getRawMessage() . ' on line ' . $error->getStartLine());
        }

        $this->findDefinitions($this->global, new Name(''));
    }

    /**
     * Find class, interface and trait definitions in statemnts
     */
    private function findDefinitions(?array $stmts, Name $name): void
    {
        $useStmts = [];

        foreach ($stmts as $stmt) {
            // Restart if namespace statement is found
            if ($stmt instanceof Namespace_) {
                $this->findDefinitions($stmt->stmts, new Name((string)$stmt->name));

            // Save use statement
            } elseif ($stmt instanceof Use_) {
                $useStmts[] = $stmt;

            // Save classes, interfaces and traits
            } elseif ($stmt instanceof Class_ || $stmt instanceof Interface_ || $stmt instanceof Trait_) {
                $defName = new Name(sprintf('%s\%s', $name, $stmt->name));
                $this->names[$defName->keyize()] = $defName->normalize();
                $this->defs[$defName->keyize()] = new Namespace_(
                    $name->normalize() !== '' && $name->normalize() !== '0' ? $name->createNode() : null,
                    $useStmts
                );
                $this->defs[$defName->keyize()]->stmts[] = $stmt;
            }
        }
    }

    /**
     * Get names of definitions in snippet
     *
     * @return string[]
     */
    public function getDefinitionNames(): array
    {
        return array_values($this->names);
    }

    /**
     * Check if snippet contains definition
     */
    public function hasDefinition(string $name): bool
    {
        return isset($this->defs[(new Name($name))->keyize()]);
    }

    /**
     * Get pars tree for class/interface/trait
     *
     * @return Namespace_[]
     * @throws RuntimeException If $name does not exist
     */
    public function read(string $name): array
    {
        if (!$this->hasDefinition($name)) {
            throw new RuntimeException(sprintf('Unable to read <%s>, not found.', $name));
        }

        return [$this->defs[(new Name($name))->keyize()]];
    }

    /**
     * Get parse tree for the complete snippet
     *
     * @return \PhpParser\Node[]|null
     */
    public function readAll(): ?array
    {
        return $this->global;
    }
}
