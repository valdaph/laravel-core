<?php

namespace Valda\Traits;

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
        $null = is_null($date) || $date === 'null';
        $equal = preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);
        $greaterThan = preg_match('/^>([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);
        $greaterThanOrEqual = preg_match('/^>=([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);
        $lessThan = preg_match('/^<([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);
        $lessThanOrEqual = preg_match('/^<=([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);
        $between = preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2}):([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date);

        $where = [];

        if ($null) {
            $where[] = [$column, '=', null];
        }

        if ($equal) {
            $where[] = [$column, '=', $date];
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

            preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2}):([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $date, $fromTo);

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
        $null = is_null($number) || $number === 'null';
        $equal = is_numeric($number);
        $greaterThan = preg_match('/^>(-?[0-9]*\.?[0-9]+)$/', $number);
        $greaterThanOrEqual = preg_match('/^>=(-?[0-9]*\.?[0-9]+)$/', $number);
        $lessThan = preg_match('/^<(-?[0-9]*\.?[0-9]+)$/', $number);
        $lessThanOrEqual = preg_match('/^<=(-?[0-9]*\.?[0-9]+)$/', $number);
        $between = preg_match('/^(-?[0-9]*\.?[0-9]+):(-?[0-9]*\.?[0-9]+)$/', $number);

        $where = [];

        if ($null) {
            $where[] = [$column, '=', null];
        }

        if ($equal) {
            $where[] = [$column, '=', $number];
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

            preg_match('/^(-?[0-9]*\.?[0-9]+):(-?[0-9]*\.?[0-9]+)$/', $number, $fromTo);

            $where[] = [$column, '>=', $fromTo[1]];
            $where[] = [$column, '<=', $fromTo[2]];
        }

        return $where;
    }
}
