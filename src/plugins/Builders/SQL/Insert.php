<?php
//**************************************************************************************
//**************************************************************************************
/**
 * SQL Insert Class
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Builders\SQL;

//**************************************************************************************
/**
 * SQL Insert Class
 */
//**************************************************************************************
class Insert extends Core
{
    //=========================================================================
	// Class Memebers
    //=========================================================================
    protected $sql_type = 'insert';
    protected $table = false;
    protected $values = [];

    //=========================================================================
    //=========================================================================
    // Into Method
    //=========================================================================
    //=========================================================================
    public function Into($table)
    {
        if ($table) {
            $this->table = $table;
        }
        return $this;
    }

    //=========================================================================
    //=========================================================================
    // Values Method
    //=========================================================================
    //=========================================================================
    public function Values($values)
    {
        return $this->AddItem($this->values, $values);
    }

    //=========================================================================
    //=========================================================================
    // Get SQL Method
    //=========================================================================
    //=========================================================================
    public function GetSQL()
    {
        if (!$this->table) {
	    	trigger_error('No table has been specified with the Into() method.');
	        return false;
	    }
        if (!$this->values) {
	    	trigger_error('No insert values have been specified with the Values() method.');
	        return false;
	    }

        //-------------------------------------------------------
        // Parse 
        //-------------------------------------------------------
        $fields = [];
        $values = [];
        foreach ($this->values as $field => $val) {
            $fields[] = $field;
            if (is_array($val)) {
                $values[] = $this->AddBindParam($val[0], $val[1]);
            }
            else {
                $values[] = $this->AddBindParam($val);
            }
        }

        //-------------------------------------------------------
        // Fields
        //-------------------------------------------------------
        if (count($fields) > 1) {
            $fields = implode(', ', $fields);
        }
        else if (count($fields) == 1) {
            $fields = $fields[0];
        }
        else {
            $fields = '';
        }

        //-------------------------------------------------------
        // Values
        //-------------------------------------------------------
        if (count($values) > 1) {
            $values = implode(', ', $values);
        }
        else if (count($fields) == 1) {
            $values = $values[0];
        }
        else {
            $values = '';
        }

        //-------------------------------------------------------
        // Build Final SQL
        //-------------------------------------------------------
		$strsql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";

        //-------------------------------------------------------
        // Return SQL
        //-------------------------------------------------------
		return $strsql;
	}

}
