<?php

namespace Finances\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Finances\Bundle\AccountBundle\Importer\ImporterFactory;

class AccountController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction ()
    {
        $import = $this->import();
        return array('activities' => $import['activities'], 'information' => $import['information']);
    }

    /**
     * @Route("/visual")
     * @Template()
     */
    public function visualAction ()
    {
        $import = $this->import();
        return array('activities' => $import['activities'], 'information' => $import['information']);
    }

    private function import ()
    {
        $importer = ImporterFactory::make('LaBanquePostale', 'releve.csv');
        if (!isset($importer)) {
            return array();
        }
        return $importer->import();
    }
}
