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
     * @Route("{method}/{filters}", defaults={"method" = "list", "filters"= ""}, name="dashboard")
     * @Template()
     */
    public function indexAction($method, $filters)
    {
        $this->importer = ImporterFactory::make('LaBanquePostale', 'releve.csv');
        $this->accountFilter = new AccountFilter();
        $this->setData($filters);

        return $this->render(
            'FinancesAccountBundle:Account:'.$method.'.html.twig',
            array(
                'entries'          => $this->data['entries'],
                'information'      => $this->data['information'],
                'availableFilters' => $this->accountFilter->getFilters(),
                'method'           => $method,
                'activeFilters'    => $filters,
            )
        );
    }

    private function setData($filters)
    {
        $this->data = $this->importer->import();

        if ($filters != "") {
            $filters = array_unique(preg_split("/[\+]+/", $filters));
            if (empty($filters)) {
                return;
            }

            $this->data['entries'] = $this->accountFilter
                ->addFilters($filters)
                ->filter($this->data['entries']);
        }
    }
}
