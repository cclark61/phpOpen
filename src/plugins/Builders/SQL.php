<?php
//**************************************************************************************
//**************************************************************************************
/**
 * SQL Builder Class
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Builders;

//**************************************************************************************
/**
 * SQL Class
 */
//**************************************************************************************
class SQL
{

    //=========================================================================
    //=========================================================================
    // Select Method
    //=========================================================================
    //=========================================================================
    public static function Select($data_source=false)
    {
		return new SQL\Select($data_source);
	}

    //=========================================================================
    //=========================================================================
    // Insert Method
    //=========================================================================
    //=========================================================================
    public static function Insert($data_source=false)
    {
		return new SQL\Insert($data_source);
	}

    //=========================================================================
    //=========================================================================
    // Update Method
    //=========================================================================
    //=========================================================================
    public static function Update($data_source=false)
    {
		return new SQL\Update($data_source);
	}

    //=========================================================================
    //=========================================================================
    // Delete Method
    //=========================================================================
    //=========================================================================
    public static function Delete($data_source=false)
    {
		return new SQL\Delete($data_source);
	}

}
