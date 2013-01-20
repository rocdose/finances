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
    public function indexAction()
    {
        $importer = ImporterFactory::make('LaBanquePostale', 'releve.csv');
        if (!isset($importer)) {
            return array();
        }
        $import = $importer->import();
        return array('activities' => $import['activities'], 'information' => $import['information']);
    }
}
