<?php

namespace Finances\Bundle\AccountBundle\Importer;

interface ImporterInterface
{
    /**
     * Imports file
     *
     * @return array
     */
    public function import ();

    /**
     * Gets whatever information is available on the account from the file
     *
     * @return array
     */
    public function getAccountInformation ();

    /**
     * Gets deposits, withdrawals, etc. from the file
     *
     * @return array
     */
    public function getAccountActivities ();
}
