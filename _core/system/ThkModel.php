<?php
/**
 * THK Analytics - free/libre analytics platform
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @link http://thk.kanzae.net/analytics/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 *
 * This program has been developed on the basis of the Research Artisan Lite.
 */

/**
 * Research Artisan Lite: Website Access Analyzer
 * Copyright (C) 2009 Research Artisan Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * @copyright Copyright (C) 2009 Research Artisan Project
 * @license GNU General Public License (see license.txt)
 * @author ossi
 */

/**
 * THK Base Model
 */
abstract class ThkModel {
/* ------------------------------------------------------------------------ */

/* -- Define -- */
	/**
	 * CHR_PLACEHOLDER
	 * @var string
	 */
	const CHR_PLACEHOLDER = '?';
	/**
	 * KEY_CONDITION
	 * @var string
	 */
	const KEY_CONDITION = 'condition';
	/**
	 * KEY_ORDER
	 * @var string
	 */
	const KEY_ORDER = 'order';
	/**
	 * KEY_GROUP
	 * @var string
	 */
	const KEY_GROUP = 'group';
	/**
	 * KEY_GROUP
	 * @var string
	 */
	const KEY_SUB_GROUP = 'subgroup';
	/**
	 * COLUMN_KEY
	 * @var string
	 */
	const COLUMN_KEY = 'id';
	/**
	 * COLUMN_CREATE
	 * @var string
	 */
	const COLUMN_CREATE = 'created_on';
	/**
	 * COLUMN_UPDATE
	 * @var string
	 */
	const COLUMN_UPDATE = 'updated_on';
	/**
	 * TABLE_NOTFOUND_ERR_CODE
	 * @var int
	 */
	const TABLE_NOTFOUND_ERR_CODE = 1146;
	/**
	 * TABLE_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const TABLE_NOTFOUND_ERR_MSG = 'Not Found Table';
	/**
	 * DUPLICATE_COLUMN_ERR_CODE
	 * @var int
	 */
	const DUPLICATE_COLUMN_ERR_CODE = 1060;
	/**
	 * DUPLICATE_COLUMN_ERR_MSG
	 * @var string
	 */
	const DUPLICATE_COLUMN_ERR_MSG = 'Duplicate column';
	/**
	 * LOADFILE_NOTFOUND_ERR_CODE * @var int
	 */
	const LOADFILE_NOTFOUND_ERR_CODE = 9930;
	/**
	 * LOADFILE_NOTFOUND_ERR_MSG
	 * @var string
	 */
	const LOADFILE_NOTFOUND_ERR_MSG = 'Not Found LoadFile';
/* ------------------------------------------------------------------------ */

/* -- Private Property -- */
	/**
	 * conn
	 * @var link_identifier
	 */
	private $_conn = null;
	/**
	 * data(row)
	 * @var array
	 */
	private $_data = array();
	/**
	 * dataAll(rows)
	 * @var array
	 */
	private $_dataAll = array();
	/**
	 * table
	 * @var string
	 */
	private $_table = null;
	/**
	 * noCreate
	 * @var boolean
	 */
	private $_noCreate = null;
	/**
	 * fileName
	 * @var string
	 */
	private $_fileName = null;
	/**
	 * options (where)
	 * @var array
	 */
	private $_options = null;
	/**
	 * notNullColumns
	 * @var array
	 */
	private $_notNullColumns = null;
	/**
	 * ignore
	 * @var boolean
	 */
	private $_ignore = false;
/* ------------------------------------------------------------------------ */

/* -- Public Method -- */
	/**
	 * Constructer
	 * @param string $table Table Name
	 * @param boolean $noCreate Create Table Flg
	 * @param string $fileName Filename for SQL
	 */
	public function __construct( $table, $noCreate=false, $fileName=null ) {
		if( !self::isDatabaseDefined() ) {
			 throw new ThkException( ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_MSG, ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_CODE );
		}
		$this->_table = constant( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) . $table;
		$this->_noCreate = $noCreate;
		$this->_fileName = $fileName !== null ? $fileName . '.sql' : $table . '.sql';
		$this->_dbConnect();
		$this->_loadColumns( $this->_table );
		$this->_notNullColumns = $this->_getNotNullColumns( $this->_fileName );
	}

	/**
	 * Destructer
	 */
	public function __destruct() {
		$this->_dbDisconnect();
	}

	/**
	 * setValue
	 * @param string $key Column Name
	 * @param string $value Data Value
	 */
	public final function setValue( $key, $value ) {
		if( array_key_exists( $key, $this->_data ) ) {
			$this->_data[$key] = $value;
		}
		else {
			throw new ThkException( __FUNCTION__ . ': Not found column: \'' . $key . '\' (table: ' . $this->_table . ')', ThkConfig::CONTINUE_ERR_CODE, true );
		}
	}

	/**
	 * getValue
	 * @param string $key Column Name
	 * @return string $value Data Value
	 */
	public final function getValue( $key ) {
		if( array_key_exists( $key, $this->_data ) ) {
			return $this->_data[$key];
		}
		else {
			throw new ThkException( __FUNCTION__ . ': Not found column: \'' . $key . '\' (table: ' . $this->_table . ')', ThkConfig::CONTINUE_ERR_CODE, true );
		}
	}

	/**
	 * find
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return array $data Result(row)
	 */
	public final function find( $select='*', $options=null ) {
		$conditions = array();
		if( isset( $options['condition'] ) && is_array( $options['condition'] ) ) {
			if( $options['condition'][0] === null || $options['condition'][0] === '' ) {
				unset( $options['condition'] );
			}
			else {
				$conditions[] = $options['condition'][0];
				foreach( $options['condition'] as $key => $value ) {
					if( $key != 0 ) $conditions[] = $value;
				}
				$options['condition'] = $conditions;
			}
		}
		$this->_options = $options;
		$rs = $this->findQuery( $select, $options );
		while( $row = $this->fetchRow( $rs ) ) {
			foreach( $this->_data as $column => $value ) {
				if( !empty( $row[$column] ) ) {
					$this->_data[$column] = $row[$column];
				}
			}
			$this->setRow();
		}
		$this->freeResult( $rs );
		return $this->_data;
	}

	/**
	 * find
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return array $dataAll Result(rows)
	 */
	public final function findAll( $select='*', $options=null ) {
		$conditions = array();
		if( isset( $options['condition'] ) && is_array( $options['condition'] ) ) {
			if( $options['condition'][0] === null || $options['condition'][0] === '' ) {
				unset( $options['condition'] );
			}
			else {
				$conditions[] = $options['condition'][0];
				foreach( $options['condition'] as $key => $value ) {
					if( $key != 0 ) $conditions[] = $value;
				}
				$options['condition'] = $conditions;
			}
		}
		$this->_options = $options;
		$rs = $this->findQuery( $select, $options );
		while( $row = $this->fetchRow( $rs ) ) {
			$this->clearData();
			foreach( $this->_data as $column => $value ) {
				$this->_data[$column] = $row[$column];
			}
			$this->setRow();
		}
		$this->freeResult( $rs );
		return $this->_dataAll;
	}

	/**
	 * save
	 * @return array $data Result(row)
	 */
	public final function save() {
		if( $this->_data['id'] === null ) {
			$this->_insert();
		}
		else {
			$this->_update();
		}
		return $this->_data;
	}

	/**
	 * delete
	 */
	public final function delete() {
		$this->_delete();
	}

	/**
	 * createTable
	 * @param string $table Table Name
	 */
	public final function createTable( $table=null ) {
		if( $table === null ) {
			$table = $this->_table;
		}
		else {
			$table = constant( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) . $table;
		}
		$this->_loadSql( $this->_getCreateSqlFile( $this->_fileName ), $table );
	}

	/**
	 * loadSql
	 * @param string $file Load Filename
	 * @param string $oldFile Load Filename (For Old Verion Database)
	 */
	public final function loadSql( $file, $oldFile ) {
		$table = $this->_table;
		$version = $this->_getServerVersion();
		$loadFile = ThkUtil::versionCompareMysql( $version[1], '4.1', '>=' ) ? $file : $oldFile;
		$this->_loadSql( $loadFile, $table );
	}

	/**
	 * loadSqlData
	 */
	public final function loadSqlData() {
		$table = $this->_table;
		$loadFile = THK_LOAD_SQL_DIR . $this->_fileName;
		if( file_exists( $loadFile ) ) {
			$this->destroy();
			$this->_loadSql( $loadFile, $table );
		}
	}

	/**
	 * _set_sql_mode
	 * @return boolean
	 */
	public final function _set_sql_mode() {
		/* Debug
		//$query = "SET GLOBAL sql_mode='STRICT_TRANS_TABLES', SESSION sql_mode='STRICT_TRANS_TABLES'";
		//$query = "SET GLOBAL sql_mode='', SESSION sql_mode=''";
		$query = "SET SESSION sql_mode='STRICT_TRANS_TABLES'";
		$this->query( $query );

		$query = "SHOW VARIABLES LIKE 'sql_mode'";
		$rs = $this->query( $query );
		var_dump( $this->fetchRow( $rs ) );
		$this->freeResult( $rs );
		*/

		$query = "SET SESSION sql_mode=''";
		return $this->query( $query );
	}

	/**
	 * destroy
	 * @return resource $rs Resource
	 */
	public final function destroy() {
		$query = 'TRUNCATE TABLE ' . $this->_escapeName( $this->_table );
		return $this->query( $query );
	}

	/**
	 * drop
	 * @return resource $rs Resource
	 */
	public final function drop() {
		$query = 'DROP TABLE ' . $this->_escapeName( $this->_table );
		return $this->query( $query );
	}

	/**
	 * droptmp
	 * @return resource $rs Resource
	 */
	public final function droptmp() {
		$query = 'DROP TEMPORARY TABLE temporary';
		return $this->query( $query );
	}
	/**
	 * showTablesCount
	 * @return resource $rs Resource
	 */
	public final function showTablesCount() {
		$query = 'SHOW TABLES FROM ' . $this->_escapeName( constant( ThkConfig::DATABASE_DEFINE_DB_NAME ) ) . ' LIKE "' . $this->_table . '"';
		$rs = mysqli_query( $this->_conn, $query );
		$row = mysqli_num_rows( $rs );
		$this->freeResult( $rs );
		return $row;
	}

	/**
	 * getDataSize
	 * @return float $dataSize Table Data Size
	 */
	public final function getDataSize() {
		$dataLength = 0;
		$indexLength = 0;
		$query = 'SHOW TABLE STATUS FROM ' . $this->_escapeName( constant( ThkConfig::DATABASE_DEFINE_DB_NAME ) );
		$rs = $this->query( $query );
		while( $row = $this->fetchRow( $rs ) ) {
			if( $row['Name'] === $this->_table ) {
				$dataLength = $row['Data_length'];
				$indexLength = $row['Index_length'];
				break;
			}
		}
		$this->freeResult( $rs );
		return $dataLength + $indexLength;
	}

	/**
	 * checkMySQLVersion
	 * @return boolean checkResult
	 */
	public final function checkMySQLVersion() {
		$rtn = true;
		$version = $this->_getServerVersion();
		if( ThkUtil::versionCompareMysql( $version[1], ThkConfig::SUPPORT_MYSQLVERSION, '<' ) ) $rtn = false;
		if( !$rtn ) {
			throw new ThkException( ThkConfig::MYSQL_NOTSUPPORT_VERSION_ERR_MSG . ' => ' . $version[1], ThkConfig::MYSQL_NOTSUPPORT_VERSION_ERR_CODE, true );
		}
		return $rtn;
	}

	/**
	 * setIgnore
	 * @param boolean $ignore Ignore Keyword Use
	 */
	public function setIgnore( $ignore ) {
		$this->_ignore = $ignore;
	}

	/**
	 * getIgnore
	 * @return boolean $ignore Ignore Keyword Use
	 */
	public function getIgnore() {
		return $this->_ignore;
	}

	/**
	 * isDatabaseDefined
	 * @return boolean $databaseDefined Defined Check
	 */
	public static function isDatabaseDefined() {
		return (
			!defined( ThkConfig::DATABASE_DEFINE_HOST ) ||
			!defined( ThkConfig::DATABASE_DEFINE_USER ) ||
			!defined( ThkConfig::DATABASE_DEFINE_PASS ) ||
			!defined( ThkConfig::DATABASE_DEFINE_DB_NAME ) ||
			!defined( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX )
		) ? false : true;
	}
/* ------------------------------------------------------------------------ */

/* -- Protected Method -- */
	/**
	 * setKey
	 * @param string $key Column Name
	 */
	protected final function setKey( $key ) {
		if( !array_key_exists( $key, $this->_data ) ) {
			$this->_data[$key] = null;
		}
		else {
			throw new ThkException( __FUNCTION__ . ': Already defined column: \'' . $key . '\' (table: ' . $this->_table . ')', ThkConfig::CONTINUE_ERR_CODE, true );
		}
	}

	/**
	 * setTable
	 * @param string $table Table Name
	 */
	protected final function setTable( $table ) {
		$this->_table = constant( ThkConfig::DATABASE_DEFINE_TABLE_PREFIX ) . $table;
	}

	/**
	 * getTable
	 * @return string $table Table Name
	 */
	protected final function getTable() {
		return $this->_table;
	}

	/**
	 * getData
	 * @return array $data Data(row)
	 */
	protected final function getData() {
		return $this->_data;
	}

	/**
	 * getDataAll
	 * @return array $data Data(rows)
	 */
	protected final function getDataAll() {
		return $this->_dataAll;
	}

	/**
	 * clearData
	 */
	protected final function clearData() {
		foreach( $this->_data as $key => $value ) {
			$this->_data[$key] = null;
		}
	}

	/**
	 * setRow
	 */
	protected final function setRow() {
		$this->_dataAll[] = $this->_data;
	}

	/**
	 * findQuery
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return resource $rs Resource
	 */
	protected final function findQuery( $select='*', $options=null ) {
		$query = 'SELECT ' . $select;
		$query .= ' FROM ' . $this->_escapeName( $this->_table );
		$query .= $this->_makeOption( $options );
		return $this->query( $query );
	}

	/**
	 * createSubquery
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return resource $rs Resource
	 */
	protected final function createSubquery( $select='*', $options=null ) {
		$query = 'SELECT ' . $select;
		$query .= ' FROM ' . $this->_escapeName( $this->_table );
		$query .= $this->_makeOption( $options );
		return $query;
	}

	/**
	 * findSubquery
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return resource $rs Resource
	 */
	protected final function findSubquery( $select='*', $subquery=null, $options=null ) {
		$query = 'SELECT ' . $select;
		$query .= ' FROM (' . $subquery . ') sub ';
		$query .= $this->_makeOption( $options );
		return $this->query( $query );
	}

	/**
	 * createTmpTable
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return boolean
	 */
	protected final function createTmpTable( $select='*', $options=null ) {
		$query = 'CREATE TEMPORARY TABLE temporary AS';
		$query .= ' SELECT ' . $select;
		$query .= ' FROM ' . $this->_escapeName( $this->_table );
		$query .= $this->_makeOption( $options );
		return mysqli_query( $this->_conn, $query );
	}

	/**
	 * findTmpTable
	 * @param string $select SQL Column
	 * @param array $options SQL Where
	 * @return resource $rs Resource
	 */
	protected final function findTmpTable( $select='*', $options=null ) {
		$query = 'SELECT ' . $select;
		$query .= ' FROM temporary';
		$query .= $this->_makeOption( $options );
		$ret = $this->query( $query );
		$this->droptmp();
		return $ret;
	}

	/**
	 * query
	 * @param string $query SQL
	 * @return resource $rs Resource
	 */
	protected final function query( $query ) {
		$rs = mysqli_query( $this->_conn, $query );
		if( !$rs ) {
			$errno = mysqli_errno( $this->_conn );
			switch( $errno ) {
			case self::TABLE_NOTFOUND_ERR_CODE:
				if( !$this->_noCreate ) {
					$this->createTable();
					$rs = mysqli_query( $this->_conn, $query );
				}
				else {
					throw new ThkException( $query . ':' . mysqli_error( $this->_conn ), $errno );
				}
				break;
			default:
				throw new ThkException( $query . ':' . mysqli_error( $this->_conn ), $errno, true );
				break;
			}
		}
		return $rs;
	}

	/**
	 * execute
	 * @param string $sql SQL
	 * @return resource $rs Resource
	 */
	protected final function execute( $sql ) {
		$this->_begin();
		$rs = mysqli_query( $this->_conn, $sql );
		if( !$rs ) {
			$this->_rollback();
			$errno = mysqli_errno( $this->_conn );
			throw new ThkException( $sql . ':' . mysqli_error( $this->_conn ), $errno, true );
		}
		$this->_commit();
		return $rs;
	}

	/**
	 * fetchRow
	 * @param resource $rs Resource
	 * @return array $result Result
	 */
	protected final function fetchRow( $rs ) {
		return mysqli_fetch_array( $rs, MYSQLI_ASSOC );
	}

	/**
	 * freeResult
	 * @param resource $rs Resource
	 * @return boolean $result Result
	 */
	protected final function freeResult( $rs ) {
		return mysqli_free_result( $rs );
	}

	/**
	 * escapeString
	 * @param string $value Value
	 * @return string $value Escape Value
	 */
	protected final function escapeString( $value ) {
		$rtn = $value;
		if( trim( $value ) !== '' ) {
			if( !ThkUtil::checkEncoding( $value ) ) {
				throw new ThkException( ThkConfig::ENCODING_INVALID_ERR_MSG . ' : invalid value is => "' . $value. '"', ThkConfig::ENCODING_INVALID_ERR_CODE, true );
			}
		}
		if( is_string( $value ) || trim( $value ) === '') {
			$rtn = '\'' . mysqli_real_escape_string( $this->_conn, $value ) . '\'';
		}
		return $rtn;
	}
/* ------------------------------------------------------------------------ */

/* -- Private Method -- */
	/**
	 * loadColumns
	 * @param string $table Table Name
	 */
	private function _loadColumns( $table ) {
		$rs = $this->query( 'SHOW FULL FIELDS FROM ' . $this->_escapeName( $table ) );
		while( $row = $this->fetchRow( $rs ) ) {
			$this->_data[$row['Field']] = null;
		}
		$this->freeResult( $rs );
	}

	/**
	 * dbConnect
	 */
	private function _dbConnect() {
		try {
			$this->_conn = mysqli_connect( constant( ThkConfig::DATABASE_DEFINE_HOST ),
				constant( ThkConfig::DATABASE_DEFINE_USER ),
				constant( ThkConfig::DATABASE_DEFINE_PASS ),
				constant( ThkConfig::DATABASE_DEFINE_DB_NAME )
				);
		}
		catch( Exception $ex ) {
			throw new ThkException( ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_MSG, ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_CODE, true );
		}
		$this->_selectDb( constant( ThkConfig::DATABASE_DEFINE_DB_NAME ) );
	}

	/**
	 * dbDisconnect
	 */
	private function _dbDisconnect() {
		if( $this->_conn !== null ) mysqli_close( $this->_conn );
	}

	/**
	 * selectDb
	 * @param string $dbname Database Name
	 */
	private function _selectDb( $dbname ) {
		if( !mysqli_select_db( $this->_conn,$dbname ) ) {
			throw new ThkException( ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_MSG, ThkConfig::DATABASE_CONFIG_UNDEFINED_ERR_CODE, true );
		}
		$version = $this->_getServerVersion();
		if( $this->_checkMysqlSetCharset( $version[1] ) ) {
			if( !mysqli_set_charset($this->_conn, ThkConfig::MYSQL_CHARSET ) ) {
				throw new ThkException( ThkConfig::DATABASE_SET_CHARSET_FAILED_ERR_MSG, ThkConfig::DATABASE_SET_CHARSET_FAILED_ERR_CODE, true );
			}
		}
		else {
			if( ThkUtil::versionCompareMysql( $version[1], '4.1', '>=' ) ) {
				$this->query( 'SET NAMES ' . ThkConfig::MYSQL_CHARSET, $this->_conn );
			}
		}
	}

	/**
	 * insert
	 * @return resource $rs Resource
	 */
	private function _insert() {
		$table = $this->_table;
		$data = $this->_data;
		$notNullColumns = $this->_notNullColumns;
		$columns = '';
		$values = '';
		foreach( $data as $key => $value ) {
			if( $key != self::COLUMN_KEY ) {
				if( $key == self::COLUMN_CREATE || $key == self::COLUMN_UPDATE ) {
					$columns = $columns . $key. ', ';
					$values = $values . 'NOW()'. ', ';
				}
				else {
					$save = false;
					if( trim( $value ) != '' ) $save = true;
					if( trim( $value ) == '' && !in_array( $key, $notNullColumns ) ) $save = true;
					if( $save ) {
						$columns = $columns . $key . ', ';
						$values = $values . $this->escapeString( $value ) . ', ';
					}
				}
			}
		}
		$sql = 'INSERT ';
		if( $this->_ignore ) $sql .= 'IGNORE ';
		$sql .= 'INTO ' . $this->_escapeName( $table ) . ' (' . substr( $columns, 0, strlen( $columns ) - 2 ) . ') VALUES (' . substr( $values, 0, strlen( $values ) - 2 ) . ') ';
		return $this->execute( $sql );
	}

	/**
	 * update
	 * @return resource $rs Resource
	 */
	private function _update() {
		$table = $this->_table;
		$data = $this->_data;
		$options = $this->_options;
		$notNullColumns = $this->_notNullColumns;
		$sets = '';
		$option = '';
		foreach( $data as $key => $value ) {
			if( $key != self::COLUMN_KEY && $key != self::COLUMN_CREATE ) {
				if( $key == self::COLUMN_UPDATE ) {
					$sets = $sets . $key . ' = NOW()' . ', ';
				}
				else {
					$save = false;
					if( trim( $value ) !== '') $save = true;
					if( trim( $value ) === '' && !in_array( $key, $notNullColumns ) ) $save = true;
					if( $save ) {
						$sets = $sets . $key . ' = ' . $this->escapeString( $value ) . ', ';
					}
				}
			}
		}
		if( $options !== null ) $option = $this->_makeOption( $options );
		$sql = 'UPDATE ' . $this->_escapeName( $table ) . ' SET ' . substr( $sets, 0, strlen( $sets ) - 2 ) . $option;
		return $this->execute( $sql );
	}

	/**
	 * delete
	 * @return resource $rs Resource
	 */
	private function _delete() {
		$table = $this->_table;
		$options = $this->_options;
		$option = '';
		if( $options !== null ) $option = $this->_makeOption( $options );
		$sql = ' DELETE FROM ' . $this->_escapeName( $table ) . ' ' . $option;
		return $this->execute( $sql );
	}

	/**
	 * begin
	 * @return resource $rs Resource
	 */
	private function _begin() {
		$sql = 'BEGIN';
		return mysqli_query( $this->_conn, $sql );
	}

	/**
	 * commit
	 * @return resource $rs Resource
	 */
	private function _commit() {
		$sql = 'COMMIT';
		return mysqli_query( $this->_conn, $sql );
	}

	/**
	 * rollback
	 * @return resource $rs Resource
	 */
	private function _rollback() {
		$sql = 'ROLLBACK';
		return mysqli_query( $this->_conn, $sql );
	}

	/**
	 * makeOption
	 * @param array $options SQL Where
	 * @return array $options SQL Where
	 */
	private function _makeOption( $options ) {
		$option = '';
		if( $options !== null ) {
			if( isset( $options[self::KEY_CONDITION] ) ) {
				$conditions = $options[self::KEY_CONDITION];
				if( is_array( $conditions ) ) {
					$replaces = array();
					foreach( $conditions as $k => $v ) {
						if( $k > 0 ) $replaces[] = $this->escapeString( $v );
					}
					$option .= ' WHERE ' . $this->_replacePlaceHolder( $conditions[0], $replaces );
				}
			}
			if( isset( $options[self::KEY_GROUP] ) ) {
				$groups = $options[self::KEY_GROUP];
				$option .= ' GROUP BY ' . $groups;
			}
			elseif( isset( $options[self::KEY_SUB_GROUP] ) ) {
				$subgroups = $options[self::KEY_SUB_GROUP];
				$option .= ' GROUP BY ' . $subgroups;
			}
			if( isset( $options[self::KEY_ORDER] ) ) {
				$orders = $options[self::KEY_ORDER];
				$option .= ' ORDER BY ' . $orders;
			}
		}
		return $option;
	}

	/**
	 * replacePlaceHolder
	 * @param string $subject Subject
	 * @param string $replaces Replaces
	 * @return string $replace Replace Value
	 */
	private function _replacePlaceHolder( $subject, $replaces ) {
		$rtn = '';
		$start = 0;
		foreach( $replaces as $k => $v ) {
			$point = stripos( $subject, self::CHR_PLACEHOLDER, $start );
			if( $point > 0 ) {
				$rtn = $rtn . str_ireplace( self::CHR_PLACEHOLDER, $v, substr( $subject, $start , $point + 1 - $start ) );
				$start = $point + 1;
			}
			else {
				break;
			}
		}
		$rtn = $rtn . substr( $subject, $start );
		return $rtn;
	}

	/**
	 * loadSql
	 * @param string $file File Name
	 * @param string $table Table Name
	 */
	private function _loadSql( $file, $table ) {
		if( !file_exists( $file ) ) {
			throw new ThkException( self::LOADFILE_NOTFOUND_ERR_MSG . ' : ' . $file, self::LOADFILE_NOTFOUND_ERR_CODE, true );
		}
		$fp = fopen( $file, 'rb' );
		$query = fread( $fp, filesize( $file ) );
		fclose( $fp );
		$query = ThkUtil::convertEncoding( $query, 'EUC-JP, SJIS, JIS, ASCII, UTF-8' );
		$query = str_replace( self::CHR_PLACEHOLDER, $this->_escapeName( $table ), $query );
		$query = preg_replace( '/\\r|\\n|\\r\\n/', ' ', $query );
		$rtn = preg_match_all( '/.+?;/', $query, $querys );
		if( $rtn !== false ) {
			foreach( $querys as $values ) foreach( $values as $value ) $this->query( $value );
		}
	}

	/**
	 * getServerVersion
	 * @result array $dbtype, $version Database Version
	 */
	private function _getServerVersion() {
		$ver = null;
		$ret = array();
		$query = 'SELECT version()';
		$rs = $this->query( $query );
		while( $row = $this->fetchRow( $rs ) ) {
			$ver = $row['version()'];
		}
		$this->freeResult( $rs );
		$ret[] = stripos( $ver, 'mariadb' ) !== false ? 'mariadb' : 'mysql';
		$ret[] = substr( $ver, 0, strcspn( $ver, '-' ) );
		return $ret;
	}

	/**
	 * getCreateSqlFile
	 * @param string $fileName File Name
	 * @result string $fileName File Name
	 */
/*
	private function _getCreateSqlFile( $fileName ) {
		if( ThkUtil::versionCompareMysql( $this->_getServerVersion(), '4.1', '>=' ) ) {
			if( ThkUtil::versionCompareMysql( $this->_getServerVersion(), '5.5', '>=' ) ) {
				$createSqlDir = THK_CREATE_SQL_55DIR;
			}
			else {
				$createSqlDir = THK_CREATE_SQL_DIR;
			}
		}
		else {
			$createSqlDir = THK_CREATE_SQL_OLDDIR;
		}
		return $createSqlDir . $fileName;
	}
*/
	private function _getCreateSqlFile( $fileName ) {
		$version = $this->_getServerVersion();
		if( ThkUtil::versionCompareMysql( $version[1], '5.4', '<=' ) ) {
			if( ThkUtil::versionCompareMysql( $version[1], '4.1', '<' ) ) {
				$createSqlDir = THK_CREATE_SQL_OLDDIR;
			}
			else {
				$createSqlDir = THK_CREATE_SQL_54DIR;
			}
		}
		else {
			$createSqlDir = THK_CREATE_SQL_DIR;
		}
		return $createSqlDir . $fileName;
	}

	/**
	 * getNotNullColumns
	 * @param string $fileName File Name
	 * @result array $notNullColumns Not Null Columns
	 */
	private function _getNotNullColumns( $fileName ) {
		$notNullColumns = array();
		$file = $this->_getCreateSqlFile( $fileName );
		$read = false;
		$fp = fopen( $file, 'rb' );
		while( !feof( $fp ) ) {
			$line = fgets( $fp, 4096 );
			if( substr_count( $line, 'CREATE' ) > 0) $read = true;
			if( $read ) if( substr_count( $line, 'NOT NULL' ) > 0 ) $notNullColumns[] = substr( $line, 0, strpos( $line, ' ' ) );
			if( substr_count( $line, self::COLUMN_CREATE ) > 0 ) break;
		}
		fclose( $fp );
		return $notNullColumns;
	}

	/**
	 * checkMysqlSetCharset
	 * @param string $mysqlVersion MySQL Version
	 * @result boolean CheckResult
	 */
	private function _checkMysqlSetCharset( $mysqlVersion ) {
		return(
			$mysqlVersion !== null &&
			function_exists( 'mysqli_set_charset' ) &&
			ThkUtil::versionComparePhp( PHP_VERSION, '5.3.0', '>=' ) &&
			ThkUtil::versionCompareMysql( $mysqlVersion, '5.0.7', '>=' )
		) ? true : false;
	}

	/**
	 * escapeName
	 * @param string $name Name
	 * @result string EscapeResult
	 */
	private function _escapeName( $name ) {
		return '`' . $name . '`';
	}
/* ------------------------------------------------------------------------ */
}
?>
