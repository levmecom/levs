<?php

/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-29 10:49
 *
 * 项目：rm  -  $  - dbDriverMysqli.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

namespace lev\db;

use mysqli;

class dbDriverMysqli
{
	var $tablepre;
	var $version = '';
	var $drivertype = 'mysqli';
	var $querynum = 0;
	var $slaveid = 0;
	var $curlink;
	var $link = array();
	var $config = array();
	var $sqldebug = array();
	var $map = array();

	function db_mysql($config = array()) {
		if(!empty($config)) {
			$this->set_config($config);
		}
	}

	function set_config($config) {
		$this->config = &$config;
		$this->tablepre = $config['1']['tablepre'];
	}

	function connect($serverid = 1) {

		if(empty($this->config) || empty($this->config[$serverid])) {
			$this->halt('config_db_not_found');
		}

		$this->link[$serverid] = $this->_dbconnect(
			$this->config[$serverid]['dbhost'],
			$this->config[$serverid]['dbuser'],
			$this->config[$serverid]['dbpw'],
			$this->config[$serverid]['dbcharset'],
			$this->config[$serverid]['dbname'],
			$this->config[$serverid]['pconnect']
			);
		$this->curlink = $this->link[$serverid];

	}

	function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect, $halt = true) {
		if ($pconnect === '1') $dbhost = 'p:' . $dbhost;
		$link = new mysqli();
		if(!$link->real_connect($dbhost, $dbuser, $dbpw, $dbname, null, null, MYSQLI_CLIENT_COMPRESS)) {
			$halt && $this->halt('notconnect', $this->errno());
		} else {
			$this->curlink = $link;
			$link->options(MYSQLI_OPT_LOCAL_INFILE, false);
			if($this->version() > '4.1') {
				$link->set_charset($dbcharset ? $dbcharset : $this->config[1]['dbcharset']);
				$serverset = $this->version() > '5.0.1' ? 'sql_mode=\'\',' : '';
				$serverset .= 'character_set_client=binary';
				$serverset && $link->query("SET $serverset");
			}
		}
		return $link;
	}

	function table_name($tablename) {
		if(!empty($this->map) && !empty($this->map[$tablename])) {
			$id = $this->map[$tablename];
			if(!$this->link[$id]) {
				$this->connect($id);
			}
			$this->curlink = $this->link[$id];
		} else {
			$this->curlink = $this->link[1];
		}
		return $this->tablepre.$tablename;
	}

	function select_db($dbname) {
		return $this->curlink->select_db($dbname);
	}

	function fetch_array($query, $result_type = MYSQLI_ASSOC) {
		return $query ? $query->fetch_array($result_type) : null;
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	public function query($sql, $silent = false, $unbuffered = false) {
        if(defined('LEV_DEBUG')) {
			$starttime = microtime(true);
		}

		if('UNBUFFERED' === $silent) {
			$silent = false;
			$unbuffered = true;
		} elseif('SILENT' === $silent) {
			$silent = true;
			$unbuffered = false;
		}

		$resultmode = $unbuffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT;

		if(!($query = $this->curlink->query($sql, $resultmode))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($silent, 0, 5) != 'RETRY') {
				$this->connect();
				return $this->curlink->query($sql, 'RETRY'.$silent);
			}
			if(!$silent) {
				$this->halt($this->error(), $this->errno(), $sql);
			}
		}

        if(defined('LEV_DEBUG')) {
			$this->sqldebug[] = array($sql, number_format((microtime(true) - $starttime), 6), debug_backtrace(), $this->curlink);
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return $this->curlink->affected_rows;
	}

	function error() {
		return (($this->curlink) ? $this->curlink->error : mysqli_error());
	}

	function errno() {
		return intval(($this->curlink) ? $this->curlink->errno : mysqli_errno());
	}

	function result($query, $row = 0) {
		if(!$query || $query->num_rows == 0) {
			return null;
		}
		$query->data_seek($row);
		$assocs = $query->fetch_row();
		return $assocs[0];
	}

	function num_rows($query) {
		$query = $query ? $query->num_rows : 0;
		return $query;
	}

	function num_fields($query) {
		return $query ? $query->field_count : null;
	}

	function free_result($query) {
		return $query ? $query->free() : false;
	}

	function insert_id() {
		return ($id = $this->curlink->insert_id) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = $query ? $query->fetch_row() : null;
		return $query;
	}

	function fetch_fields($query) {
		return $query ? $query->fetch_field() : null;
	}

	function version() {
		if(empty($this->version)) {
			$this->version = $this->curlink->server_info;
		}
		return $this->version;
	}

	function escape_string($str) {
		return $this->curlink->escape_string($str);
	}

	function close() {
		return $this->curlink->close();
	}

	function halt($message = '', $code = 0, $sql = '') {
		throw new DBException($message, $code, $sql);
	}

}

?>