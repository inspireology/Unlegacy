<?php
//TODO: replace with mysqli_query
//TODO: fix mysqli errors
class SQLQuery
{
    protected $_dbHandle;
    protected $_result;

    /**
     * Connects to database
     * @param $address
     * @param $account
     * @param $pwd
     * @param $name
     * @return int
     */
    function connect($address, $account, $pwd, $name)
    {
        $this->_dbHandle = @mysqli_connect($address, $account, $pwd);
        if ($this->_dbHandle != 0) {
            if (mysqli_select_db($name, $this->_dbHandle)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }


    /**
     * Disconnects from database
     * @return int
     */
    function disconnect()
    {
        if (@mysqli_close($this->_dbHandle) != 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return array
     */
    function selectAll()
    {
        $query = 'select * from `' . $this->_table . '`';
        return $this->query($query);
    }

    /**
     * @param $id
     * @return array
     */
    function select($id)
    {
        $query = 'select * from `' . $this->_table . '` where `id` = \'' . mysqli_real_escape_string($id) . '\'';
        return $this->query($query, 1);
    }


    /**
     * Custom SQL Query
     * @param $query
     * @param int $singleResult
     * @return array
     */
    function query($query, $singleResult = 0)
    {
        //TODO: Warn of using query string instead of parameterised query
        $this->_result = mysqli_query($query, $this->_dbHandle);

        if (preg_match("/select/i", $query)) {
            $result = array();
            $table = array();
            $field = array();
            $tempResults = array();
            $numOfFields = mysqli_num_fields($this->_result);
            for ($i = 0; $i < $numOfFields; ++$i) {
                array_push($table, mysqli_field_table($this->_result, $i));
                array_push($field, mysqli_field_name($this->_result, $i));
            }


            while ($row = mysqli_fetch_row($this->_result)) {
                for ($i = 0; $i < $numOfFields; ++$i) {
                    $table[$i] = trim(ucfirst($table[$i]), "s");
                    $tempResults[$table[$i]][$field[$i]] = $row[$i];
                }
                if ($singleResult == 1) {
                    mysqli_free_result($this->_result);
                    return $tempResults;
                }
                array_push($result, $tempResults);
            }
            mysqli_free_result($this->_result);
            return ($result);
        }
    }

    /**
     * Get number of rows
     * @return int
     */
    function getNumRows()
    {
        return mysqli_num_rows($this->_result);
    }

    /**
     * Free resources allocated by a query *
     *
     */
    function freeResult()
    {
        mysqli_free_result($this->_result);
    }

    /**
     * Get error string
     * @return string
     */
    function getError()
    {
        return mysqli_error($this->_dbHandle);
    }
}
