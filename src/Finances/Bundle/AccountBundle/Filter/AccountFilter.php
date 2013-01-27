<?php

namespace Finances\Bundle\AccountBundle\Filter;

class AccountFilter
{
    const GROCERIES = 0;
    const HOUSING   = 1;
    const MOVIES    = 2;
    const CLOTHING  = 3;
    const PHONE     = 4;
    
    private $availableFilters = array(
        self::GROCERIES => 'Groceries',
        self::MOVIES    => 'Movies',
        self::CLOTHING  => 'Clothing',
        self::PHONE     => 'Phone',
    );

    /**
     * Filters account entries
     * Runs a regular expression on the description of every account entry
     *
     * @param $data
     *
     * @return Filtered data
     */
    public function filter($data)
    {
        if (empty($this->filters)) {
            return $data;
        }

        $this->filteredData = array();

        foreach ($this->filters as $filter) {
            $this->filteredData = $this->filteredData + $this->doFilter($data, $this->availableFilters[$filter]);
        }

        uasort($this->filteredData, array($this, 'compareDates'));
        return $this->filteredData;
    }

    /**
     * Compare two entries dates
     */
    private function compareDates($a, $b) 
    {
        $timeA = strtotime($a['date']);
        $timeB = strtotime($b['date']);

        if ($timeA == $timeB) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Adds filters
     *
     * @param $filters
     */
    public function addFilters($filters)
    {
        $this->filters = array();

        foreach ($filters as $filter) {
            if (($key = array_search($filter, $this->availableFilters)) === false) {
                continue;
            }
            $this->filters[] = $key;
        }

        return $this;
    }

    private function doFilter($data, $filterName)
    {
        $filteredData = $data;

        foreach ($data as $index => $datum) {
            $methodName = "get".$filterName."Patterns";
            $patterns = $this->$methodName();
            foreach ($patterns as $pattern) {
                if (preg_match("/".$pattern."/i", $datum['description']) != 1) {
                    unset($filteredData[$index]);
                }
            }
        }

        return $filteredData;
    }

    public function getGroceriesPatterns()
    {
        return array(
            "\b(franprix|monoprix|8 a huit)\b",
        );
    }

    public function getMoviesPatterns()
    {
        return array(
            "\b(mk2|gaumont|ugc)\b",
        );
    }

    public function getPhonePatterns()
    {
        return array(
            "\b(orange)\b",
        );
    }

    public function getClothingPatterns()
    {
        return array(
            "\b(benetton|gal.lafayette|celio)\b",
        );
    }

    public function getFilters()
    {
        return $this->availableFilters;
    }
}
