<?php

declare(strict_types=1);

namespace Kenny1911\ClassVisibilityFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ClassVisibilityFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    use ConfigurableFixerTrait;

    public function getName(): string
    {
        return 'Kenny1911/'.parent::getName();
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM]);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Add @api, @internal or @psalm-internal', []);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $index = 0;

        while (null !== ($classIndex = $tokens->getNextTokenOfKind($index, [[T_CLASS], [T_INTERFACE], [T_TRAIT], [T_ENUM]]))) {
            $index = $classIndex;

            while (null !== ($prevIndex = $tokens->getPrevNonWhitespace($classIndex))) {
                if (in_array($tokens[$prevIndex]->getId(), [T_READONLY, T_ABSTRACT, T_FINAL], true)) {
                    $classIndex = $prevIndex;
                    continue;
                }

                break;
            }

            $docCommentIndex = $tokens->getPrevNonWhitespace($classIndex);
            $docCommentToken = null !== $docCommentIndex && $tokens[$docCommentIndex]->isGivenKind(T_DOC_COMMENT) ? $tokens[$docCommentIndex] : null;
            $apiOrInternalDocComment = $this->apiOrInternalDocComment($tokens, $classIndex);

            if ('' === $apiOrInternalDocComment) {
                continue;
            }

            if (null === $docCommentToken) {
                // If no doc-comment
                $tokens->insertAt($classIndex, new Token([T_DOC_COMMENT, "/**\n$apiOrInternalDocComment\n */\n"]));
                $index += 1;
            } elseif (
                // If doc-comment not contains @api, @internal or @psalm-internal
                false === str_contains($docCommentToken->getContent(), '@api')
                && false === str_contains($docCommentToken->getContent(), '@internal')
                && false === str_contains($docCommentToken->getContent(), '@psalm-internal')
            ) {
                $tokens[$docCommentIndex] = new Token([
                    T_DOC_COMMENT,
                    mb_substr($docCommentToken->getContent(), 0, -3).$apiOrInternalDocComment."\n */",
                ]);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('defaultVisibility', 'Default class visibility.'))
                ->setAllowedValues(['api', 'internal', 'psalm-internal', 'internal+psalm-internal'])
                ->setDefault('internal+psalm-internal')
                ->getOption(),
        ]);
    }

    private function apiOrInternalDocComment(Tokens $tokens, int $classIndex): string
    {
        $visibility = $this->configuration['defaultVisibility'] ?? 'internal+psalm-internal';

        return match ($visibility) {
            'api' => ' * @api',
            'internal' => ' * @internal',
            'psalm-internal' => ' * '.$this->psalmInternalDocComment($tokens, $classIndex),
            'internal+psalm-internal' => " * @internal\n * ".$this->psalmInternalDocComment($tokens, $classIndex),
        };
    }

    private function psalmInternalDocComment(Tokens $tokens, int $classIndex): string
    {
        $docComment = '@psalm-internal';
        $classNamespace = $this->getClassNamespace($tokens, $classIndex);

        if (null !== $classNamespace) {
            $docComment .= ' '.$classNamespace;
        }

        return $docComment;
    }

    private function getClassNamespace(Tokens $tokens, int $classIndex): ?string
    {
        $namespaceTokens = $tokens->findGivenKind(T_NAMESPACE, end: $classIndex);

        if ([] === $namespaceTokens) {
            return null;
        }

        $namespaceIndex = max(array_keys($namespaceTokens));
        $namespaceStartIndex = $tokens->getNextNonWhitespace($namespaceIndex);

        if (null === $namespaceStartIndex) {
            return null;
        }

        $namespace = '';
        for ($i = $namespaceStartIndex; $i < $classIndex; ++$i) {
            if (in_array($tokens[$i]->getId(), [T_STRING, T_NS_SEPARATOR], true)) {
                $namespace .= $tokens[$i]->getContent();
                continue;
            }

            if (';' === $tokens[$i]->getContent()) {
                break;
            }

            return null;
        }

        $namespace = trim($namespace);

        if ('' === $namespace) {
            return null;
        }

        return $namespace;
    }
}
