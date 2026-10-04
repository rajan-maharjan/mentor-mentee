<?php
class common extends connection{
	function getDBFields($tableName){
		$this->sql="DESCRIBE ".TBL_PREFIX."$tableName";
		$this->execute($this->sql);
		$returnArray = array();
		while($rows = $this->fetch()){
			$fieldArray = array();
			array_push($returnArray,$rows->Field);
		}
		return $returnArray;	
	}
	function selectRow($tableName, $fields, $where=1, $orderby="", $groupby="", $startlimit='', $perPageLimit=''){ // pass the parameter fields in array	
		$return=$this->select($tableName, $fields, $where, $orderby, $groupby, $startlimit, $perPageLimit);		
		
		if($return)
			return $return[0];
		else
			return array();
	}
	
	
	function getUniqueURL($tableName, $primaryKey, $fieldName, $titleValue){
		$url=functions::getUrlCode($titleValue);
		$rsltResult=$this->selectRow($tableName,array($primaryKey), $fieldName."'='".$url."'");
		if($rsltResult->$primaryKey){
			$url=$url.'-'.$rsltResult->$primaryKey.rand(1,9999);
		} 
			return $url;
	}
	
	function select($tableName, $fields=array("*"), $where=1, $orderby="", $groupby="", $startlimit='', $perPageLimit=''){
		
		$this->sql="SELECT ".join(", ",$fields)." FROM ".TBL_PREFIX."$tableName ";			
		if($where!='' || $where!=1){
			$this->sql.=" WHERE ".$where;		
		}
		
		if($groupby!='')		
			$this->sql.=" GROUP BY $groupby";
			
		if($orderby!='')		
			$this->sql.=" ORDER BY $orderby";
			
		if($perPageLimit)
			$this->sql.=" LIMIT $startlimit, $perPageLimit";
		
        #echo "<br /><br />".$this->sql;
		$exec=$this->execute();
		
		$returnArray=array();
		while($row=@mysqli_fetch_object($exec)){		
			array_push($returnArray,$row);			
			}		
		return $returnArray;
		}
		
	
	function selectJoin($tableOne, $tableTwo, $fields1=array(), $fields2=array(), $joinType, $commonFieldJoinCondition, $where='', $orderby='',$groupby='', $startlimit='', $perPageLimit=''){
		$fieldsText="1 ";
		
		if(count($fields1)>0){
			$fieldsText.=", t1.".join(", t1.",$fields1);
		}
		
		if(count($fields2)>0){
			$fieldsText.=", t2.".join(", t2.",$fields2);
		}
				
		$this->sql="SELECT ".$fieldsText." FROM ".TBL_PREFIX."$tableOne t1 $joinType join ".TBL_PREFIX."$tableTwo t2 on ".$commonFieldJoinCondition;		
		if($where!='' || $where!=1){
			$this->sql.=" WHERE ".$where;		
		}
		if($orderby!='')		
			$this->sql.=" ORDER BY $orderby";
		if($groupby!='')		
			$this->sql.=" GROUP BY $groupby";	
		if($perPageLimit)
			$this->sql.=" LIMIT $startlimit, $perPageLimit";
		
		#echo "<br /><br />".$this->sql;			
		$exec=$this->execute();
		$returnArray=array();
		while($row=mysqli_fetch_object($exec)){
			array_push($returnArray,$row);			
			}		
		return $returnArray;
		}
		
	function insert($tableName,$array){
		$fquery = "";
		foreach($array as $field=>$value){
			$fquery.=$field."='".addslashes($value)."', ";
		}
		$fquery=trim(substr($fquery,0,strlen($fquery)-2)??'');
		
		$this->sql="INSERT INTO ".TBL_PREFIX."$tableName set ".$fquery;
		#echo "<br /><br />".$this->sql;
		$this->execute();
		return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	}
	
	function update($tableName,$array,$whereCondition,$exceptFieldArray=array()){	
		$fquery='';			
		foreach($array as $field=>$value){
			if(!in_array($field,$exceptFieldArray))
				$fquery.=$field."='".addslashes($value)."', ";
		}		
		$fquery=trim(substr($fquery,0,strlen($fquery)-2)??'');//remove the comma of last field
		$this->sql="UPDATE ".TBL_PREFIX."$tableName set ".$fquery." WHERE $whereCondition";
		#echo $this->sql."<br />";
		$this->execute();
	}
	
	function updateStatus($tableName, $whereCondition){	
		$this->sql="UPDATE ".TBL_PREFIX."$tableName set is_active = (1-is_active) where $whereCondition";		
		$this->execute();			
		}
	
	function destructAll($tableName){
		$this->sql="describe ".TBL_PREFIX."$tableName";		
		$result=$this->execute();	
			
		while($rows = $this->fetch()){
			$fieldName=$rows->Field;
			unset($this->$fieldName);					
			}
		
	}	
	
	function delete($table, $where){
		//$this->sql="DELETE from ".TBL_PREFIX."$table where $where";
		$this->sql="update ".TBL_PREFIX."$table set deleted_by='".$_SESSION['session_user_id']."', deleted_on=sysdate(), is_active='2' where $where";	
		$this->execute();
		return true;
	}
	
	function __destruct() {
		
	}
}
    ?>