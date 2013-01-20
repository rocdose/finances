<?php

namespace Finances\Bundle\AccountBundle\Importer;

class ImporterFactory
{
    public static function make ($name, $fileName)
    {
        switch ($name) {
            case 'LaBanquePostale':
                return new LaBanquePostaleImporter($fileName);
            default:
                return null;
        }
    }
}
