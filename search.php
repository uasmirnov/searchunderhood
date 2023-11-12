<?php

/**
 * Необходимо написать на php скрипт, который получит результат поиска с этой
 * страницы: https://search.ipaustralia.gov.au/trademarks/search/advanced
 *
 * Необходимо чтобы скрипт, который мы можем вызвать из консоли с передачей
 * параметра для поиска, запускал поиск по полю «word» например со значением: abc.
 * Получал результат поиска, проходил по всем страницам, сохранял данные и выводил
 * результат.
 */

include_once 'Importer.php';
include_once 'OutputerSelector.php';
include_once 'WriterSelector.php';
include_once 'vendor/autoload.php';

use Test\Import\Importer;
use Test\Import\OutputerSelector;
use Test\Import\WriterSelector;

$importer = new Importer($argv[1] ?? '', WriterSelector::FILE);
try {
    $importer
        ->prepare()
        ->validate()
        ->run();

    $outputer = OutputerSelector::select(OutputerSelector::TYPE_CONSOLE);
    $outputer
        ->prepare()
        ->output();
} catch (Exception $e) {
    die($e->getMessage());
}
