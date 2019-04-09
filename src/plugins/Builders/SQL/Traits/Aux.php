<?php
//**************************************************************************************
//**************************************************************************************
/**
 * SQL Auxillary Trait
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Builders\SQL\Traits;

//**************************************************************************************
/**
 * Aux Trait
 */
//**************************************************************************************
trait Aux
{
    //=========================================================================
    //=========================================================================
    // DB Type Is Valid?
    //=========================================================================
    //=========================================================================
    public static function DbTypeIsValid(String $db_type)
    {
        if (!in_array($db_type, ['mysql', 'pgsql', 'oracle', 'sqlsrv'])) {
            return false;
        }
        return true;
    }

    //=========================================================================
    //=========================================================================
    // Is Valid Operator
    //=========================================================================
    //=========================================================================
    public static function IsValidOperator($op)
    {
        if (!is_scalar($op) || (string)$op === '') {
            return false;
        }
        $op = strtolower($op);
        $ops = [
            '=', 
            '!=', 
            '<>', 
            '<', 
            '<=', 
            '>', 
            '>=', 
            'in', 
            'not in', 
            'like', 
            'not like', 
            'between', 
            'not between',
            'is null',
            'is not null'
        ];
        if (!in_array($op, $ops)) {
            return false;
        }
        return true;
    }

    //=========================================================================
    //=========================================================================
    // Add a Bind Parameter
    //=========================================================================
    //=========================================================================
    public static function AddBindParam(String $db_type, Array &$params, $value, $type='s')
    {
        //-----------------------------------------------------------------
        // Is Database Type Valid?
        //-----------------------------------------------------------------
        if (!self::DbTypeIsValid($db_type)) {
            throw new \Exception('Invalid database type.');
        }

        //-----------------------------------------------------------------
        // Validate that Value is Scalar
        //-----------------------------------------------------------------
        if (!is_scalar($value)) {
            throw new \Exception('Value must be a scalar value.');
        }

        //-----------------------------------------------------------------
        // Which Class is using this trait?
        //-----------------------------------------------------------------
        // (i.e. How do we add the bind parameter?)
        //-----------------------------------------------------------------
        switch ($db_type) {

            //-----------------------------------------------------------------
            // MySQL
            //-----------------------------------------------------------------
            case 'mysql':
            case 'mysqli':
                if (count($params) == 0) {
                    $params[] = '';
                }
                $params[0] .= $type;
                $params[] = $value;
                return '?';
                break;

            //-----------------------------------------------------------------
            // PgSQL
            //-----------------------------------------------------------------
            case 'pgsql':
                $index = count($params);
                $ph = '$' . $index;
                if (isset($params[$index])) {
                    throw new \Exception('An error occurred trying to add the PostgreSQL bind parameter. Parameter index already in use.');
                }
                $params[$index] = $value;
                return $ph;
                break;

            //-----------------------------------------------------------------
            // Oracle
            //-----------------------------------------------------------------
            case 'oracle':
                $index = count($params);
                $ph = 'p' . $index;
                if (isset($params[$ph])) {
                    throw new \Exception('An error occurred trying to add the Oracle bind parameter. Parameter index already in use.');
                }
                $params[$ph] = $value;
                return ':' . $ph;
                break;

            //-----------------------------------------------------------------
            // Default
            //-----------------------------------------------------------------
            default:
                $params[] = $value;
                return '?';
                break;

        }
    }

    //=========================================================================
    //=========================================================================
    // Add a Bind Parameters
    //=========================================================================
    //=========================================================================
    public static function AddBindParams(String $db_type, Array &$params, Array $values, $type='s')
    {
        $place_holders = '';
        foreach ($values as $value) {
            $tmp_ph = self::AddBindParam($db_type, $params, $value, $type);
            $place_holders .= ($place_holders) ? (', ' . $tmp_ph) : ($tmp_ph);
        }
        return $place_holders;
    }

    //=========================================================================
    //=========================================================================
    // AndOr Method
    //=========================================================================
    //=========================================================================
    protected static function AndOr(String $andor, $condition)
    {
        //-----------------------------------------------------------------
        // Validate AndOr
        //-----------------------------------------------------------------
        $andor = strtolower($andor);
        if ($andor != 'and' && $andor != 'or') {
            throw new \Exception('Invalid And/Or parameter.');
        }

        //-----------------------------------------------------------------
        // Is Condition Empty?
        //-----------------------------------------------------------------
        if (!$condition) {
            return false;
        }

        //-----------------------------------------------------------------
        // Return Condtion with And / Or attached
        //-----------------------------------------------------------------
        if ($andor == 'and') {
            return 'and ' . $condition;
        }
        else {
            return 'or ' . $condition;
        }
    }

    //=========================================================================
    //=========================================================================
	// Add Item Raw Method
    //=========================================================================
    //=========================================================================
	protected static function AddItem(&$var, $val)
	{
		if ($val) {
			if (is_array($val)) {
				$var = array_merge($var, $val);
			}
			else {
				$var[] = $val;
			}
		}
	}

    //=========================================================================
    //=========================================================================
	// Add Item CSC Method
    //=========================================================================
	// Detects comma separated items and pulls them apart
	// and adds them individually
    //=========================================================================
    //=========================================================================
	protected static function AddItemCSC(&$var, $value)
	{
    	if ($value) {
        	$values = [];

            //-----------------------------------------------------------------
            // Scalar Value
            //-----------------------------------------------------------------
        	if (is_scalar($value)) {
            	$values = explode(',', $value);
            }
            //-----------------------------------------------------------------
            // Array of Values
            //-----------------------------------------------------------------
            else if (is_array($value)) {
                foreach ($value as $tmp_value) {
                    $tmp_value = trim($tmp_value);
                    if ($tmp_value) {
                    	self::AddItemCSC($var, $tmp_value);
                    }
                }
                return true;
            }

            //-----------------------------------------------------------------
            // Scalar value exploded by comma into an array
            //-----------------------------------------------------------------
            if ($values && is_iterable($values)) {
                foreach ($values as $tmp_value) {
                    $tmp_value = trim($tmp_value);
                    if ($tmp_value) {
                    	self::AddItem($var, $tmp_value);
                    }
                }
            }
        }

        return true;
	}

    //=========================================================================
    //=========================================================================
    // Format Comma Separated Clause Method
    //=========================================================================
    //=========================================================================
    protected static function FormatCSC($clause, $values)
    {
        if ($values) {
    		return "{$clause}\n  " . implode(",\n  ", $values);
        }

        return false;
    }

    //=========================================================================
    //=========================================================================
	// Add SQL Clause Method
    //=========================================================================
    //=========================================================================
	protected static function AddSQLClause(&$strsql, $clause)
	{
		if ($clause) {
    		$strsql .= $clause . "\n";
            return true;
        }

        return false;
    }

}
