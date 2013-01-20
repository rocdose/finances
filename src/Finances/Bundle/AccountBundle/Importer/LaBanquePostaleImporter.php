<?php

namespace Finances\Bundle\AccountBundle\Importer;

class LaBanquePostaleImporter implements ImporterInterface
{

    public function __construct ($fileName)
    {
        $this->path = dirname(__FILE__).'/../../../../../../files/LaBanquePostale/';
        $this->filePath = $this->path.$fileName;
    }
        
    public function import ()
    {
        $this->import = array();

        if (($this->handle = fopen($this->filePath, "r")) !== FALSE) {
            $this->getAccountInformation();
            for ($i = 0 ; $i < 3 ; $i++) { // ignore the next 3 lines
                fgetcsv($this->handle, 1000, ";");
            }
            $this->getAccountActivities();
            fclose($this->handle);
        }

        return $this->import;
    }

    public function getAccountInformation ()
    {
        $information = array();
        $row = 0;

        while (($data = fgetcsv($this->handle, 1000, ";")) !== FALSE) {
            switch (++$row) {
                case 1:
                    $this->import['information']['number'] = $data[1];
                    break;
                case 2:
                    $this->import['information']['type'] = $data[1];
                    break;
                case 3:
                    $this->import['information']['currency'] = $data[1];
                    break;
                case 4:
                    $this->import['information']['date'] = $data[1];
                    break;
                case 5:
                    $this->import['information']['credit'] = $data[1];
                    break;
            }
            if ($row >= 5) { // account information stop at line 5
                break;
            }
        }
    }

    public function getAccountActivities ()
    {
        while (($data = fgetcsv($this->handle, 1000, ";")) !== FALSE) {
            $this->import['activities'][] = array(
                'date' => $data[0],
                'description' => $data[1],
                'amount' => $data[2],
            );
        }
    }
}
