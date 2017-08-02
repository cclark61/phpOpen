<?php
//**************************************************************************************
//**************************************************************************************
/**
 * SQL Delete Class
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
 * SQL Delete Class
 */
//**************************************************************************************
class Delete extends Core
{

    //=========================================================================
    //=========================================================================
    // Constructor Method
    //=========================================================================
    //=========================================================================
    public function __construct($table)
    {
	    $this->sql_type = 'delete';
	}

    //=========================================================================
    //=========================================================================
    // Get Method
    //=========================================================================
    //=========================================================================
    public function GetSQL()
    {
		$strsql = 'DELETE FROM ' . implode(', ', $this->from) . ' ';
		//$where = 
		return $strsql;

	}

}
