<?php

declare(strict_types=1);

namespace Sai;

use Dagger\Client;
use Dagger\Container;

final class PHPRuntime
{
    private string $version = '8.2';
    private OS $os = OS::Alpine;
    private string $variant = 'cli';

    private Container $container;

    public function __construct(private Client $dagger)
    {
    }

    public static function create(Client $dagger): self
    {
        return new self($dagger);
    }

    public function version(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function os(OS $os): self
    {
        $this->os = $os;
        return $this;
    }

    public function fpm(): self
    {
        $this->variant = 'fpm';
        $this->createContainer();
        return $this;
    }

    public function cli(): self
    {
        $this->variant = 'cli';
        $this->createContainer();
        return $this;
    }

    public function zts(): self
    {
        $this->variant = 'zts';
        $this->createContainer();
        return $this;
    }

    public function apache(): self
    {
        if (!$this->os->isDebian()) {
            $this->os = OS::Debian;
        }

        $this->variant = 'apache';
        $this->createContainer();
        return $this;
    }

    private function createContainer(): void
    {
        $this->container = $this->dagger->container()->from($this->makeTag());
    }
    private function makeTag(): string
    {
        return sprintf('php:%s-%s-%s', $this->version, $this->variant, $this->os->value);
    }

    public function getContainer(): Container
    {
        if (!isset($this->container)) {
            $this->createContainer();
        }

        return $this->container;
    }

    private function cmd(string $cmd)
    {
        return ["/bin/sh", "-c", $cmd];
    }

    public function withPackages(string ... $packageNames): self
    {
        $cmd = $this->os->isAlpine() ? 'apk add --no-cache ' : 'apt-get update && apt-get install -y ';

        $this->container = $this->getContainer()->withExec(
            $this->cmd($cmd . implode(' ', $packageNames))
        );

        return $this;
    }

    public function withExtensions(string ...$extensions): self
    {
        $extensionInstaller = $this->dagger
            ->container()
            ->from('mlocati/php-extension-installer')
            ->file('/usr/bin/install-php-extensions');

        $this->container = $this->container
            ->withFile('/usr/local/bin/install-php-extensions', $extensionInstaller)
            ->withExec(
                $this->cmd('install-php-extensions ' . implode(' ', $extensions))
            );

        return $this;
    }
}