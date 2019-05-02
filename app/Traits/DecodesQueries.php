<?php

namespace Valda\Traits;

use Carbon\Carbon;

trait DecodesQueries
{
    /**
     * Decode date query to where parameters.
     *
     * @param  string|null  $date
     * @param  string  $column
     * @return array
     */
    protected function decodeDateQuery($date, $column)
    {
        $dateRegex = '([0-9]{4}-[0-9]{2}-[0-9]{2})';

        $null = is_null($date) || $date === 'null';
        $notNull = $date === '!null';
        $equal = preg_match("/^$dateRegex$/", $date);
        $notEqual = preg_match("/^!$dateRegex$/", $date);
        $greaterThan = preg_match("/^>$dateRegex$/", $date);
        $greaterThanOrEqual = preg_match("/^>=$dateRegex$/", $date);
        $lessThan = preg_match("/^<$dateRegex$/", $date);
        $lessThanOrEqual = preg_match("/^<=$dateRegex$/", $date);
        $between = preg_match("/^$dateRegex:$dateRegex$/", $date);

        $where = [];

        if ($null) {
            $where[] = [$column, '=', null];
        }

        if ($notNull) {
            $where[] = [$column, '!=', null];
        }

        if ($equal) {
            $where[] = [$column, '=', $date];
        }
        
        if ($notEqual) {
            $where[] = [$column, '!=', ltrim($date, '!')];
        }

        if ($greaterThan) {
            $where[] = [$column, '>', ltrim($date, '>')];
        }

        if ($greaterThanOrEqual) {
            $where[] = [$column, '>=', ltrim($date, '>=')];
        }

        if ($lessThan) {
            $where[] = [$column, '<', ltrim($date, '<')];
        }

        if ($lessThanOrEqual) {
            $where[] = [$column, '<=', ltrim($date, '<=')];
        }

        if ($between) {
            $fromTo = [];

            preg_match("/^$dateRegex:$dateRegex$/", $date, $fromTo);

            $where[] = [$column, '>=', $fromTo[1]];
            $where[] = [$column, '<=', $fromTo[2]];
        }

        return $where;
    }

    /**
     * Decode date time query to where parameters.
     *
     * @param  string|null  $dateTime
     * @param  string  $column
     * @return array
     */
    protected function decodeDateTimeQuery($dateTime, $column)
    {
        if ($dateWhere = $this->decodeDateQuery($dateTime, $column)) {
            foreach ($dateWhere as &$where) {
                if ($where[1] === '=' && $where[2] !== null) {
                    $where[2] = Carbon::parse($where[2])->startOfDay()->toDateTimeString();
                }

                if ($where[1] === '<' || $where[1] === '<=') {
                    $where[2] = Carbon::parse($where[2])->endOfDay()->toDateTimeString();
                }

                if ($where[1] === '>' || $where[1] === '>=') {
                    $where[2] = Carbon::parse($where[2])->startOfDay()->toDateTimeString();
                }
            }

            return $dateWhere;
        }

        $dateTimeRegex = '([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})';

        $null = is_null($dateTime) || $dateTime === 'null';
        $notNull = $dateTime === '!null';
        $equal = preg_match("/^$dateTimeRegex$/", $dateTime);
        $notEqual = preg_match("/^!$dateTimeRegex$/", $dateTime);
        $greaterThan = preg_match("/^>$dateTimeRegex$/", $dateTime);
        $greaterThanOrEqual = preg_match("/^>=$dateTimeRegex$/", $dateTime);
        $lessThan = preg_match("/^<$dateTimeRegex$/", $dateTime);
        $lessThanOrEqual = preg_match("/^<=$dateTimeRegex$/", $dateTime);
        $between = preg_match("/^$dateTimeRegex|$dateTimeRegex$/", $dateTime);

        $where = [];

        if ($null) {
            $where[] = [$column, '=', null];
        }

        if ($notNull) {
            $where[] = [$column, '!=', null];
        }

        if ($equal) {
            $where[] = [$column, '=', $dateTime];
        }
        
        if ($notEqual) {
            $where[] = [$column, '!=', ltrim($dateTime, '!')];
        }

        if ($greaterThan) {
            $where[] = [$column, '>', ltrim($dateTime, '>')];
        }

        if ($greaterThanOrEqual) {
            $where[] = [$column, '>=', ltrim($dateTime, '>=')];
        }

        if ($lessThan) {
            $where[] = [$column, '<', ltrim($dateTime, '<')];
        }

        if ($lessThanOrEqual) {
            $where[] = [$column, '<=', ltrim($dateTime, '<=')];
        }

        if ($between) {
            $fromTo = [];

            preg_match("/^$dateTimeRegex|$dateTimeRegex$/", $dateTime, $fromTo);

            $where[] = [$column, '>=', $fromTo[1]];
            $where[] = [$column, '<=', $fromTo[2]];
        }

        return $where;
    }

    /**
     * Decode numeric query to where parameters.
     *
     * @param  string|null  $number
     * @param  string  $column
     * @return array
     */
    protected function decodeNumericQuery($number, $column)
    {
        $numberRegex = '(-?[0-9]*\.?[0-9]+)';

        $null = is_null($number) || $number === 'null';
        $notNull = $number === '!null';
        $equal = is_numeric($number);
        $notEqual = preg_match("/^!$numberRegex$/", $number);
        $greaterThan = preg_match("/^>$numberRegex$/", $number);
        $greaterThanOrEqual = preg_match("/^>=$numberRegex$/", $number);
        $lessThan = preg_match("/^<$numberRegex$/", $number);
        $lessThanOrEqual = preg_match("/^<=$numberRegex$/", $number);
        $between = preg_match("/^$numberRegex:$numberRegex$/", $number);

        $where = [];

        if ($null) {
            $where[] = [$column, '=', null];
        }

        if ($notNull) {
            $where[] = [$column, '!=', null];
        }

        if ($equal) {
            $where[] = [$column, '=', $number];
        }
        
        if ($notEqual) {
            $where[] = [$column, '!=', ltrim($number, '!')];
        }

        if ($greaterThan) {
            $where[] = [$column, '>', ltrim($number, '>')];
        }

        if ($greaterThanOrEqual) {
            $where[] = [$column, '>=', ltrim($number, '>=')];
        }

        if ($lessThan) {
            $where[] = [$column, '<', ltrim($number, '<')];
        }

        if ($lessThanOrEqual) {
            $where[] = [$column, '<=', ltrim($number, '<=')];
        }

        if ($between) {
            $fromTo = [];

            preg_match("/^$numberRegex:$numberRegex$/", $number, $fromTo);

            $where[] = [$column, '>=', $fromTo[1]];
            $where[] = [$column, '<=', $fromTo[2]];
        }

        return $where;
    }
}
