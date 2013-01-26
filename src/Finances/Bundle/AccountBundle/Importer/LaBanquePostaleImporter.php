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
                    $this->currentCredit = $this->lastCredit = str_replace(',', '.', $data[1]);
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
            $this->lastCredit = $this->currentCredit;
            $this->currentCredit -= str_replace(',', '.', $data[2]);
            $activity = array(
                'date'          => \DateTime::createFromFormat('d/m/Y', $data[0])->format('Y/m/d'),
                'description'   => utf8_encode($data[1]), // twig needs utf8 encoding (not happy with the ° character)
                'amount'        => $data[2],
                'lastCredit'    => $this->lastCredit,
                'currentCredit' => $this->currentCredit,
            );
            $this->import['activities'][] = $activity;
        }

        $this->import['activities'] = array_reverse($this->import['activities']);
    }
}
