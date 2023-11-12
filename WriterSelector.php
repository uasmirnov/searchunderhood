<?php

namespace Test\Import;

include_once 'WriterFile.php';

class WriterSelector
{
    const FILE = 'file';
    const DB = 'db';

    /**
     * @var array
     */
    private static array $types = [
        self::FILE => WriterFile::class,
    ];

    /**
     * @param ImporterInterface $importer
     * @param string            $writerType
     *
     * @return WriterInterface
     */
    public static function select(ImporterInterface $importer, string $writerType): WriterInterface
    {
        $writerFactory = WriterSelector::$types[$writerType];

        return new $writerFactory($importer);
    }
}
