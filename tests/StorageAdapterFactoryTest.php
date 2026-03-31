<?php

declare(strict_types=1);

namespace JoeriAbbo\LaravelPrometheusExporter\Tests;

use InvalidArgumentException;
use JoeriAbbo\LaravelPrometheusExporter\StorageAdapterFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prometheus\Exception\StorageException;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;

#[CoversClass(\JoeriAbbo\LaravelPrometheusExporter\StorageAdapterFactory::class)]
class StorageAdapterFactoryTest extends TestCase
{
    /**
     * @var StorageAdapterFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new StorageAdapterFactory();
    }

    public function testMakeMemoryAdapter(): void
    {
        $adapter = $this->factory->make('memory');
        $this->assertInstanceOf(InMemory::class, $adapter);
    }

    public function testMakeApcAdapter(): void
    {
        if (! extension_loaded('apcu')) {
            $this->expectExceptionObject(new StorageException('APCu extension is not loaded'));
        }
        if (\function_exists('apcu_enabled') && ! apcu_enabled()) {
            $this->expectExceptionObject(new StorageException('APCu is not enabled'));
        }

        $adapter = $this->factory->make('apc');
        $this->assertInstanceOf(APC::class, $adapter);
    }

    public function testMakeInvalidAdapter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The driver [moo] is not supported.');
        $this->factory->make('moo');
    }
}
