<?php
//**************************************************************************************
//**************************************************************************************
/**
 * Data Source Trait
 *
 * @package		phpOpenFW
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Database\Traits;

//**************************************************************************************
/**
 * Data Source Trait
 */
//**************************************************************************************
trait DataSource
{

	//*****************************************************************************
	// Class Members
	//*****************************************************************************
	protected $data_src = '';
	protected $data_src_data = false;
	protected $handle = false;
	protected $resource = false;
	protected $server = '127.0.0.1';
	protected $port = false;
	protected $source = false;
	protected $user = false;
	protected $pass = false;
	protected $persistent = true;
    protected $records = false;
    protected $num_recs = false;
    protected $fetch_pos = 0;

	//*****************************************************************************
	//*****************************************************************************
    // Get Object Instance
	//*****************************************************************************
	//*****************************************************************************
    public static function Instance($data_src='')
    {
	    return new static($data_src);
    }

	//*****************************************************************************
	//*****************************************************************************
    // Is Data Source Valid
	//*****************************************************************************
	//*****************************************************************************
    public function IsDataSourceValid($data_src='')
    {
		$data_src_data = self::GetDataSource($data_src);
		return (is_array($data_src_data)) ? (true) : (false);
    }

	//*****************************************************************************
	//*****************************************************************************
    // Get Data Source
	//*****************************************************************************
	//*****************************************************************************
    public function GetDataSource($data_src='')
    {
		if ($data_src != '') {
			if (isset($_SESSION[$data_src])) {
    			return $_SESSION[$data_src];
			}
		}
		else {
			if (isset($_SESSION['default_data_source'])) {
				return $_SESSION[$_SESSION['default_data_source']];
			}
		}

        return false;
    }

	//*****************************************************************************
	//*****************************************************************************
    // Set Connection Parameters
	//*****************************************************************************
	//*****************************************************************************
    public function SetConnectionParameters()
    {
        $this->handle = (!isset($this->data_src['handle'])) ? (false) : ($this->data_src['handle']);
        $this->server = (!isset($this->data_src['server'])) ? ('127.0.0.1') : ($this->data_src['server']);
        $this->port = (!isset($this->data_src['port'])) ? (389) : ($this->data_src['port']);
        $this->source = (!isset($this->data_src['source'])) ? ('') : ($this->data_src['source']);
        $this->user = (!isset($this->data_src['user'])) ? ('') : ($this->data_src['user']);
        $this->pass = (!isset($this->data_src['pass'])) ? ('') : ($this->data_src['pass']);
        $this->persistent = (!isset($this->data_src['persistent'])) ? (true) : ($this->data_src['pass']);
    }

	//*****************************************************************************
	//*****************************************************************************
    // Set Connection Parameters
	//*****************************************************************************
	//*****************************************************************************
    public function SetDataSourceHandle()
    {
        $_SESSION[$this->data_src]['handle'] = $this->handle;
    }

	//*****************************************************************************
	//*****************************************************************************
    // Get Data Source Handle
	//*****************************************************************************
	//*****************************************************************************
    public function GetDataSourceHandle()
    {
        if (isset($_SESSION[$this->data_src]['handle'])) {
            return $_SESSION[$this->data_src]['handle'];
        }

        return false;
    }

	//*****************************************************************************
	//*****************************************************************************
    // Get Connection Handle
	//*****************************************************************************
	//*****************************************************************************
    public function GetConnectionHandle()
    {
        return $this->handle;
    }

	//*****************************************************************************
	//*****************************************************************************
    // Get Query Resource
	//*****************************************************************************
	//*****************************************************************************
    public function GetResource()
    {
        return $this->resource;
    }

	//*****************************************************************************
	//*****************************************************************************
	// Reset Function
	//*****************************************************************************
	//*****************************************************************************
	public function Reset()
	{
        $this->records = false;
        $this->num_recs = false;
        $this->fetch_pos = 0;
    }

}
