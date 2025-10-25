<?php
require_once(dirname(__FILE__) . "/../common/connect.php");

class tbl_product
{
	public $id;
	public $product_name;
	public $product_type;
	public $product_detail;
	public $price_per_unit;
	public $unit_name;
	public $is_stock;
	public $create_by;
	public $create_date;
	public $update_by;
	public $update_date;
	public $is_enable;
	public $is_active;

	public function insertDB(){
		$this->is_active = 'T';
		$property = get_object_vars($this);
		$addfiled = "";
		$addvalue = "";
		foreach(array_keys($property) as $item)
		{
			if($item == 'id')
			{
				continue;
			}
			$addfiled .= $item.",";
			if($this->$item == NULL)
			{
				$addvalue .= "NULL,";
			}
			else
			{
				$addvalue .= "'".$GLOBALS["conn"]->real_escape_string($property[$item])."',";
			}
		}
		if( strlen($addfiled) > 0)
		{
			$addfiled = substr($addfiled,0,strlen($addfiled)-1);
		}
		if( strlen($addvalue) > 0)
		{
			$addvalue = substr($addvalue,0,strlen($addvalue)-1);
		}
		$sql1 = "INSERT INTO ".get_class($this)." (".$addfiled.") values(".$addvalue.")";
		if(!$GLOBALS["conn"]->query($sql1))
		{
			return false;
		}
		$this->id = $GLOBALS["conn"]->insert_id;
		return true;
	}
	public function updateDB(){
		$property = get_object_vars($this);
		$editvalue = "";
		foreach(array_keys($property) as $item)
		{
			if($item != "id")
			{
				$editvalue .= $item." = ".($this->$item == NULL ? "NULL" : "'".$GLOBALS["conn"]->real_escape_string($this->$item)."'").",";
			}
		}
		if( strlen($editvalue) > 0)
		{
			$editvalue = substr($editvalue,0,strlen($editvalue)-1);
		}
		$sql1 = "UPDATE ".get_class($this)." SET ".$editvalue." WHERE id='".$GLOBALS["conn"]->real_escape_string($this->id)."'";
		$result1 = $GLOBALS["conn"]->query($sql1);
		$totalrecord = $GLOBALS["conn"]->affected_rows;
		if($totalrecord>=0)
		{
			return true;
		}
		return false;
    }
    public function deleteDB(){
		$sql1="DELETE FROM ".get_class($this)." WHERE id='".$GLOBALS["conn"]->real_escape_string($this->id)."'";
		if(!$GLOBALS["conn"]->query($sql1))
		{
			return false;
		}
		return true;	
	}
	public function disableDB(){
		$sql1="UPDATE ".get_class($this)." SET is_enable = 'F',is_active = 'F' WHERE id='".$GLOBALS["conn"]->real_escape_string($this->id)."'";
		if(!$GLOBALS["conn"]->query($sql1))
		{
			return false;
		}
		return true;	
	}
	public function enableDB(){
		$sql1="UPDATE ".get_class($this)." SET is_enable = 'T' WHERE id='".$GLOBALS["conn"]->real_escape_string($this->id)."'";
		if(!$GLOBALS["conn"]->query($sql1))
		{
			return false;
		}
		return true;	
    }
    public function getById(){
		$sql1="SELECT * FROM ".get_class($this)." where id = '".$GLOBALS["conn"]->real_escape_string($this->id)."' 
		and is_enable = 'T'
		";
		$result1 = $GLOBALS["conn"]->query($sql1);
		$num_rows1 = $result1->num_rows;
		if($num_rows1 == 1)
		{
			$rs=$result1->fetch_array();
			foreach(array_keys($rs) as $item)
			{
				if(property_exists($this,$item))
				{
					$this->$item = $rs[$item];
				}
			}
			
			return true;
		}
		else
		{
			return false;
		}
    }
    public function getEnable(){
		$sql1="SELECT * FROM ".get_class($this)." where is_enable ='T' ";
		$result1 = $GLOBALS["conn"]->query($sql1);
		$num_rows1 = $result1->num_rows;
		if($num_rows1 < 1)
		{
			return NULL;
		}
		else
		{
			return $result1;
		}
	}
	public function getActivate(){
		$sql1="SELECT * FROM ".get_class($this)." where is_active ='T' ";
		$result1 = $GLOBALS["conn"]->query($sql1);
		$num_rows1 = $result1->num_rows;
		if($num_rows1 < 1)
		{
			return NULL;
		}
		else
		{
			return $result1;
		}
	}
}
?>