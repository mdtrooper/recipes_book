<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
	/**
     * @covers Email::__construct
     * @covers Email::fromString
     * @covers Email::ensureIsValidEmail
     */
    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertInstanceOf(
            Email::class,
            Email::fromString('user@example.com')
        );
    }

	/**
     * @covers Email::__construct
     * @covers Email::fromString
     * @covers Email::ensureIsValidEmail
     */
    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

	/**
     * @covers Email::__construct
     * @covers Email::fromString
     * @covers Email::ensureIsValidEmail
     * @covers Email::__toString
     */
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }
}
?>