<?php

namespace Test\Import;

include_once 'WriterInterface.php';

class WriterFile implements WriterInterface
{
    /**
     * @param ImporterInterface $importer
     * @param string            $path
     */
    public function __construct(
        private ImporterInterface $importer,
        private string $path = 'file.txt'
    ) {
    }

    /**
     * @return $this
     */
    public function prepare(): self
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }

        return $this;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function save(): bool
    {
        if (false === file_exists($this->path)) {
            file_put_contents($this->path, '[]');
        }

        $content = json_decode(file_get_contents($this->path), true);
        $content[] = $this->importer->getBuffer();

        $content = json_encode($content, JSON_UNESCAPED_UNICODE);

        if (false === file_put_contents($this->path, $content)) {
            throw new \Exception(sprintf('Something saving error (path = "%s")', $this->path) . "\n");
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}