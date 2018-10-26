<?php
//**************************************************************************************
//**************************************************************************************
/**
 * PostgreSQL Conditions Class
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Builders\SQL\Conditions;

//**************************************************************************************
/**
 * PgSQL Class
 */
//**************************************************************************************
class PgSQL
{
    use Conditions;
    protected static $db_type = 'pgsql';
}
