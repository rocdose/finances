<?php

namespace Finances\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Finances\Bundle\AccountBundle\Importer\ImporterFactory;
use Finances\Bundle\AccountBundle\Filter\AccountFilter;

class AccountController extends Controller
{
    /**
     * @Route("{method}/{filters}", defaults={"method" = "list", "filters"= ""})
     * @Template()
     */
    public function indexAction($method, $filters)
    {
        $this->setData($filters);
        return $this->render(
            'FinancesAccountBundle:Account:'.$method.'.html.twig',
            array('entries' => $this->data['entries'], 'information' => $this->data['information'])
        );
    }

    private function setData($filters)
    {
        $importer = ImporterFactory::make('LaBanquePostale', 'releve.csv');
        if (!isset($importer)) {
            return array();
        }
        $this->data = $importer->import();

        if ($filters != "") {
            $filters = preg_split("/[\+]+/", $filters);
            if (empty($filters)) {
                return;
            }

            $filter = new AccountFilter();
            $this->data['entries'] = $filter
                ->addFilters($filters)
                ->filter($this->data['entries']);
        }
    }
}
