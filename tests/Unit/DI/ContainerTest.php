<?php declare(strict_types=1);

use PhpFramework\DI\Container;
use PHPUnit\Framework\TestCase;

define('APP_ROOT', dirname(dirname(__DIR__)));

class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testHasNoClass()
    {
        $this->assertFalse($this->container->has('foo'));
    }

    public function testHasClass()
    {
        $this->container->set('foo', 'bar');
        $this->assertTrue($this->container->has('foo'));
    }

    public function testGetNoClassException()
    {
        $this->expectException(ReflectionException::class);
        $this->container->get('foo');
    }
}
