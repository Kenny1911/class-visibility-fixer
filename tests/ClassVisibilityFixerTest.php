<?php

declare(strict_types=1);

namespace Kenny1911\ClassVisibilityFixer\Kenny1911;

use Kenny1911\ClassVisibilityFixer\ClassVisibilityFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

final class ClassVisibilityFixerTest extends TestCase
{
    public function testClassDefaultVisibility(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            class Baz {}
            PHP,
            [],
        );
    }

    public function testClassApiVisibility(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @api
             */
            class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            class Baz {}
            PHP,
            [
                'defaultVisibility' => 'api'
            ],
        );
    }

    public function testClassInternalVisibility(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             */
            class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            class Baz {}
            PHP,
            [
                'defaultVisibility' => 'internal'
            ],
        );
    }

    public function testClassPsalmInternalVisibility(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @psalm-internal Foo\Bar
             */
            class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            class Baz {}
            PHP,
            [
                'defaultVisibility' => 'psalm-internal'
            ],
        );
    }

    public function testClassInternalAndPsalmInternalVisibility(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            class Baz {}
            PHP,
            [
                'defaultVisibility' => 'internal+psalm-internal'
            ],
        );
    }

    public function testClassWithoutNamespace(): void
    {
        $this->doTest(
            <<<PHP
            /**
             * @internal
             * @psalm-internal
             */
            class Baz {}
            PHP,
            <<<PHP
            class Baz {}
            PHP,
            [],
        );
    }

    public function testFinalClass(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            final class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            final class Baz {}
            PHP,
            [],
        );
    }

    public function testAbstractClass(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            abstract class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            abstract class Baz {}
            PHP,
            [],
        );
    }

    public function testReadonlyClass(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            readonly class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            readonly class Baz {}
            PHP,
            [],
        );
    }

    public function testFinalReadonlyClass(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            final readonly class Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            final readonly class Baz {}
            PHP,
            [],
        );
    }

    public function testInterface(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            interface Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            interface Baz {}
            PHP,
            [],
        );
    }

    public function testTrait(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            trait Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            trait Baz {}
            PHP,
            [],
        );
    }

    public function testEnum(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            enum Baz {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            enum Baz {}
            PHP,
            [],
        );
    }

    public function testManyClasses(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            trait Baz {}

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            trait Qux {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            trait Baz {}
            
            trait Qux {}
            PHP,
            [],
        );
    }

    public function testManyNamespaces(): void
    {
        $this->doTest(
            <<<PHP
            namespace Foo\Bar;

            /**
             * @internal
             * @psalm-internal Foo\Bar
             */
            trait Baz {}

            namespace Foo2\Bar2;

            /**
             * @internal
             * @psalm-internal Foo2\Bar2
             */
            trait Qux {}
            PHP,
            <<<PHP
            namespace Foo\Bar;

            trait Baz {}
            
            namespace Foo2\Bar2;
            
            trait Qux {}
            PHP,
            [],
        );
    }

    public function testAlreadyHasDocBlock(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @api
             */
            class Foo {}

            /**
             * @internal
             */
            class Bar {}

            /**
             * @psalm-internal Some\Namespace
             */
            class Baz {}

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            class Qux {}

            /**
             * Quux class description.
             *
             * @internal
             * @psalm-internal Some\Namespace
             */
            final readonly class Quux {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            /**
             * @api
             */
            class Foo {}

            /**
             * @internal
             */
            class Bar {}

            /**
             * @psalm-internal Some\Namespace
             */
            class Baz {}

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            class Qux {}

            /**
             * Quux class description.
             */
            final readonly class Quux {}
            PHP,
            [],
        );
    }

    public function testClassHasAttribute(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            #[FooAttr('bar', 'baz')]
            class Foo {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            #[FooAttr('bar', 'baz')]
            class Foo {}
            PHP,
            [],
        );
    }

    public function testClassHasMultilineAttribute(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            #[FooAttr(
                bar: 'bar',
                baz: 'baz',
            )]
            class Foo {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            #[FooAttr(
                bar: 'bar',
                baz: 'baz',
            )]
            class Foo {}
            PHP,
            [],
        );
    }

    public function testClassHasManyAttributes(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            #[FooAttr('bar', 'baz')]
            #[BarAttr]
            class Foo {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            #[FooAttr('bar', 'baz')]
            #[BarAttr]
            class Foo {}
            PHP,
            [],
        );
    }

    public function testClassHasManyAttributes2(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @internal
             * @psalm-internal Some\Namespace
             */
            #[FooAttr('bar', 'baz')]
            #[BarAttr]
            #[
                Baz(
                    date: new \DateTimeImmutable()
                ),
                Baz(
                    date: new \DateTimeImmutable()
                ),
            ]
            #[Qux1, Qux2]
            #[Qux\Quxx]
            class Foo {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            #[FooAttr('bar', 'baz')]
            #[BarAttr]
            #[
                Baz(
                    date: new \DateTimeImmutable()
                ),
                Baz(
                    date: new \DateTimeImmutable()
                ),
            ]
            #[Qux1, Qux2]
            #[Qux\Quxx]
            class Foo {}
            PHP,
            [],
        );
    }

    public function testClassHasAttributeAndDocBlock(): void
    {
        $this->doTest(
            <<<PHP
            namespace Some\Namespace;

            /**
             * @author Kenny1911 <o-muzyka@mail.ru>
             *
             * @internal
             * @psalm-internal Some\Namespace
             */
            #[FooAttr('bar', 'baz')]
            class Foo {}
            PHP,
            <<<PHP
            namespace Some\Namespace;

            /**
             * @author Kenny1911 <o-muzyka@mail.ru>
             */
            #[FooAttr('bar', 'baz')]
            class Foo {}
            PHP,
            [],
        );
    }

    /**
     * @param non-empty-string $expected
     * @param non-empty-string $code
     */
    private function doTest(string $expected, string $code, array $configuration): void
    {
        $expected = <<<PHP
        <?php
        
        
        PHP.$expected;

        $code = <<<PHP
        <?php
        
        
        PHP.$code;

        $fixer = new ClassVisibilityFixer();
        $fixer->configure($configuration);

        Tokens::clearCache();
        $tokens = Tokens::fromCode($code);

        $this->assertTrue($fixer->isCandidate($tokens));
        $fixer->fix(new \SplTempFileObject(), $tokens);

        $this->assertSame($expected, $tokens->generateCode());
    }
}
