<?php

class Validation extends Fuel\Core\Validation
{
    /**
     * Check the val is unique
     *
     * @param mixed
     * @param Array
     * @return boolean
     */
    public function _validation_unique($val, $option)
    {
        list($table, $field) = $option;
        try
        {
            $result = DB::select($field)
                        ->from($table)
                        ->where($field, '=', $val)
                        ->limit(1)
                        ->execute();
        } catch (Exception $e)
        {
            throw new \Exception("get {$table}.{$field} failed.");
        }
        return !($result->count() > 0);
    }
}
