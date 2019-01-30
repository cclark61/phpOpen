<?php
//**************************************************************************************
//**************************************************************************************
/**
 * Update Statement Test Class
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Test\Builders\SQL;
use \phpOpenFW\Builders\SQL;

//**************************************************************************************
/**
 * Update Class
 */
//**************************************************************************************
class Update
{
    //=========================================================================
    //=========================================================================
    // Test
    //=========================================================================
    //=========================================================================
    public static function Test()
    {
        //---------------------------------------------------------------
        // DB Types
        //---------------------------------------------------------------
        $db_types = [
            'mysql',
            'pgsql',
            'oracle',
            'sqlsrv'
        ];

        //---------------------------------------------------------------
        // Build SQL Update Statements for each database type
        //---------------------------------------------------------------
        foreach ($db_types as $db_type) {

            //----------------------------------------------------------------
            // Test Header
            //----------------------------------------------------------------
            $disp_db_type = ucfirst($db_type);
            print "\n-------------------------------------------------------";
            print "\n*** {$disp_db_type} Update Statements";
            print "\n-------------------------------------------------------\n\n";

            //---------------------------------------------------------------
            // Test Values
            //---------------------------------------------------------------
            $values = [
                ['field_1', 'value_1'],
                ['field_2', 'value_2']
            ];

            //---------------------------------------------------------------
            // Create / Start SQL Update Statement
            //---------------------------------------------------------------
            $query = SQL::Update('cases')
            ->SetDbType($db_type)
            ->Values($values)
            ->Value('field_2', 22, 'i')
            ->Value('field_3', 'value_3')
            ->Value('field_4', null)
            ->Where('field_id', '=', 4);

            //---------------------------------------------------------------
            // Output Query / Bind Parameters
            //---------------------------------------------------------------
            print $query . "\n";
            print_r($query->GetBindParams());
        }
    }
    
}