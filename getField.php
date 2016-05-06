<?php

/*
*
*用来获取在该主机中数据库存在某个字段的所有表
*author：hxch
date  2016-05-05
*
*/

class db_getfiled{

	private $conn;  //数据库连接

	private $databses=[]; //所有的数据库

	private $check_databses=[]; //所有需要查询数据库

	private $usedatabase;  //正在使用的数据库名

	private $data; //数据集合

	public  function __construct($host,$username,$password='')
	{
		$this->conn=mysql_connect($host,$username,$password) or die('无法连接数据库主机');
		mysql_query('set names utf8');//设置编码

		$result=mysql_query('show databases');

		while($row=mysql_fetch_assoc($result))
		{
			$this->databses[]=$row['Database'];
		}
		$this->check_databses=$this->databses;
	}

	public function getAlldatabase()
	{
		return $this->databses;
	}

	public function getCheckdatabase()
	{
		return $this->check_databses;
	}

	public function getAlldata()
	{
		return $this->data;
	}


	public function setbase($base=null)// 可以 设置要查询的数据库
	{
		if(!is_array($base)){
			die('设置查询的数据库名称有误');
		}
		foreach($base as $k=>$v){
			if(!in_array($v,$this->databses)){
				die('设置查询的数据库名有部分不存在本机上');
			}
		}
		$this->check_databses=$base;
	}


	private function _excute()//执行
	{
		$db=[];

		 foreach($this->check_databses as $k=>$v){
			  mysql_query('use '.$v);
			  $result=mysql_query('show tables ');

			  $db[$v]=[];
			  while($row=mysql_fetch_assoc($result)){

				  $res=mysql_query('desc '.$row['Tables_in_'.$v]);
				  $tmp=[];
				  while($rr=mysql_fetch_assoc($res))
				  {
                     $tmp[]=$rr['Field'];
				  }
				  $db[$v][$row['Tables_in_'.$v]]=$tmp;
			  }
		  }
		$this->data=$db;
	}

	public function query($field){
		if(!is_string($field)){
			die('查询参数错误！');
		}
		$return=[];
		$this->_excute();
		foreach($this->data as $k=>$v){
			foreach($v as $key=>$val){
				if(in_array($field,$val)){
					$return[$k][]=$key;
					continue;

				}
			}
		}
		return $return;
	}
}

$a=new db_getfiled('127.0.0.1','root','');
$a->query('iccid');



