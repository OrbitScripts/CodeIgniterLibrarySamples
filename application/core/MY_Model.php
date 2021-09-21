<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class MY_Model
 *
 * @property CI_DB $db
 *
 * @method $this select(string $select = '*', mixed $escape = null) Generates the SELECT portion of the query
 * @method $this select_max(string $select = '', string $alias = '') Generates a SELECT MAX(field) portion of a query
 * @method $this select_min(string $select = '', string $alias = '') Generates a SELECT MIN(field) portion of a query
 * @method $this select_avg(string $select = '', string $alias = '') Generates a SELECT AVG(field) portion of a query
 * @method $this select_sum(string $select = '', string $alias = '') Generates a SELECT SUM(field) portion of a query
 * @method $this distinct(bool $val = true) Sets a flag which tells the query string compiler to add DISTINCT
 * @method $this from(mixed $from) Generates the FROM portion of the query
 * @method $this join(string $table, string $cond, string $type = '', string $escape = null) Generates the JOIN portion
 *         of the query
 * @method $this where(mixed $key, mixed $value = null, bool $escape = null) Generates the WHERE portion of the query.
 *         Separates multiple calls with 'AND'.
 * @method $this or_where(mixed $key, mixed $value = null, bool $escape = null) Generates the WHERE portion of the
 *         query. Separates multiple calls with 'OR'.
 * @method $this where_in(string $key = null, array $values = null, bool $escape = null) Generates a WHERE field
 *         IN('item', 'item') SQL query, joined with 'AND' if appropriate.
 * @method $this or_where_in(string $key = null, array $values = null, bool $escape = null) Generates a WHERE field
 *         IN('item', 'item') SQL query, joined with 'OR' if appropriate.
 * @method $this where_not_in(string $key = null, array $values = null, bool $escape = null) Generates a WHERE field
 *         NOT IN('item', 'item') SQL query, joined with 'AND' if appropriate.
 * @method $this or_where_not_in(string $key = null, array $values = null, bool $escape = null) Generates a WHERE field
 *         NOT IN('item', 'item') SQL query, joined with 'OR' if appropriate.
 * @method $this like(mixed $field, string $match = '', string $side = 'both', bool $escape = null) Generates a %LIKE%
 *         portion of the query. Separates multiple calls with 'AND'.
 * @method $this not_like(mixed $field, string $match = '', string $side = 'both', bool $escape = null) Generates a NOT
 *         LIKE portion of the query. Separates multiple calls with 'AND'.
 * @method $this or_like(mixed $field, string $match = '', string $side = 'both', bool $escape = null) Generates a
 *         %LIKE% portion of the query. Separates multiple calls with 'OR'.
 * @method $this or_not_like(mixed $field, string $match = '', string $side = 'both', bool $escape = null) Generates a
 *         NOT LIKE portion of the query. Separates multiple calls with 'OR'.
 * @method $this group_start(string $not = '', string $type = 'AND ') Starts a query group.
 * @method $this or_group_start() Starts a query group, but ORs the group
 * @method $this not_group_start() Starts a query group, but NOTs the group
 * @method $this or_not_group_start() Starts a query group, but OR NOTs the group
 * @method $this group_end() Ends a query group
 * @method $this group_by(string $by, bool $escape = null) GROUP BY
 * @method $this having(string $key, string $value = null, bool $escape = null) Separates multiple calls with 'AND'.
 * @method $this or_having(string $key, string $value = null, bool $escape = null) Separates multiple calls with 'OR'.
 * @method $this order_by(string $orderby, string $direction = '', bool $escape = null) ORDER BY
 * @method $this limit(int $value, int $offset = 0) LIMIT
 * @method $this offset(int $offset) Sets the OFFSET value
 * @method $this set(mixed $key, string $value = '', bool $escape = null) Allows key/value pairs to be set for
 *         inserting or updating
 * @method string get_compiled_select(string $table = '', bool $reset = true) Compiles a SELECT query string and
 *         returns the sql.
 * @method $this get(string $table = '', string $limit = null, string $offset = null) Compiles the select statement
 *         based on the other functions called and runs the query
 * @method int count_all_results(string $table = '', bool $reset = true) Generates a platform-specific query string
 *         that counts all records returned by an Query Builder query.
 * @method $this get_where(string $table = '', string $where = null, int $limit = null, int $offset = null) Allows the
 *         where clause, limit and offset to be added directly
 * @method int insert_batch(string $table, array $set = null, bool $escape = null, int $batch_size = 100) Compiles
 *         batch insert strings and runs the queries
 * @method $this set_insert_batch(mixed $key, string $value = '', bool $escape = null) Allows key/value pairs to be set
 *         for batch inserts
 * @method string get_compiled_insert(string $table = '', bool $reset = true) Compiles an insert query and returns the
 *         sql
 * @method string get_compiled_update(string $table = '', bool $reset = true) Compiles an update query and returns the
 *         sql
 * @method int update_batch(string $table, array $set = null, string $index = null, int $batch_size = 100) Compiles an
 *         update string and runs the query
 * @method $this set_update_batch(array $key, string $index = '', bool $escape = null) Allows key/value pairs to be set
 *         for batch updating
 * @method bool empty_table(string $table = '') Compiles a delete string and runs "DELETE FROM table"
 * @method bool truncate(string $table = '') Compiles a truncate string and runs the query
 * @method string get_compiled_delete(string $table = '', bool $reset = true) Compiles a delete query string and
 *         returns the sql
 * @method mixed delete(mixed $table = '', mixed $where = '', mixed $limit = null, bool $reset_data = true) Compiles a
 *         delete string and runs the query
 * @method string dbprefix(string $table = '') Prepends a database prefix if one exists in configuration
 * @method string set_dbprefix(string $prefix = '') Set's the DB Prefix to something new without needing to reconnect
 * @method $this start_cache() Starts QB caching
 * @method $this stop_cache() Stops QB caching
 * @method $this flush_cache() Empties the QB cache
 * @method $this reset_query() Publicly-visible method to reset the QB values.
 */
class MY_Model extends CI_Model
{

    const FIELD_TYPE_INT     = 'int';
    const FIELD_TYPE_FLOAT   = 'float';
    const FIELD_TYPE_STRING  = 'string';
    const FIELD_TYPE_BOOLEAN = 'boolean';

    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * Table name
     *
     * @var string
     */
    protected $_name = null;

    /**
     * PK field(s)
     *
     * @var array
     */
    protected $_primary = null;

    /**
     * Field types
     *
     * @var array
     */
    protected $_field_types = [];

    /**
     * Cache prefix
     *
     * @var string
     */
    private $_cache_prefix = 'db_scheme/';

    /**
     * MY_Model constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->_name === null) {
            throw new Exception('Не задана таблица для модели ' . get_class($this));
        }

        $cache_name = $this->_cache_prefix . $this->_name;
        $this->load->driver('cache', ['adapter' => 'file']);
        if ($this->_name !== null) {
            if (false === ($cache_data = $this->cache->file->get($cache_name))) {
                if ($this->_primary === null) {
                    $this->_primary = [];

                    $fields = $this->db->field_data($this->_name);
                    for ($i = 0, $j = count($fields); $i < $j; $i++) {
                        if ($fields[$i]->primary_key === 1) {
                            $this->_primary[] = $fields[$i]->name;
                        }
                        $this->_field_types[$fields[$i]->name] = $fields[$i]->type;
                    }
                    if (count($this->_primary) === 0) {
                        throw new Exception('В таблице ' . $this->_name . ' не определён первичный ключ');
                    }
                } elseif (!is_array($this->_primary)) {
                    $this->_primary = [$this->_primary];
                }

                $cache_data = [
                    $this->_primary,
                    $this->_field_types,
                ];

                $this->cache->file->save($cache_name, $cache_data, 31536000); // 86400 * 365
            }
            list($this->_primary, $this->_field_types) = $cache_data;
        }
    }

    /**
     * Returns result's first row's first column
     *
     * @param bool $process
     *
     * @return string|null
     */
    public function fetchOne($process = false)
    {
        //$this->db->clear();
        $query = $this->db->get($this->_name, 1, 0);
        if ($query->num_rows() == 0) {
            return null;
        }
        $value = $query->row_array();
        $value = current($value);
        if ($process) {
            return htmlspecialchars($value);
        }

        return $value;
    }

    /**
     * Returns only the specified column (by name) from results as and array
     *
     * @param string $column
     * @param bool   $process
     *
     * @return array
     */
    public function fetchColName($column, $process = false)
    {
        //$this->db->clear();
        $query = $this->db->get($this->_name);
        $values = [];
        foreach ($query->result_array() as $row) {
            $values[] = $row[$column];
        }
        if ($process) {
            $values = array_map([
                $this,
                'processRow',
            ], $values);
        }

        return $values;
    }

    /**
     * Returns only the specified column (by number) from results as and array
     *
     * @param int  $column
     * @param bool $process
     *
     * @return array
     */
    public function fetchColNum($column = 0, $process = false)
    {
        //$this->db->clear();
        $query = $this->db->get($this->_name);
        $values = [];
        foreach ($query->result_array() as $row) {
            $row = array_values($row);
            $values[] = $row[$column];
        }
        if ($process) {
            $values = array_map([
                $this,
                'processRow',
            ], $values);
        }

        return $values;
    }

    /**
     * Returns data by PK
     *
     * @return array|null
     * @throws Exception
     */
    public function getById()
    {
        if ($this->_primary === null) {
            throw new Exception('В таблице ' . $this->_name . ' не определён первичный ключ');
        }

        $pk = func_get_args();
        if (count($this->_primary) != count($pk)) {
            throw new Exception(
                $this->_name . ': Количество полей первичного ключа не соответствует количество переданных параметров.'
            );
        }

        for ($i = 0, $j = count($this->_primary); $i < $j; $i++) {
            $this->db->where($this->_primary[$i], $pk[$i]);
        }

        return $this->fetchRow();
    }

    /**
     * Returns result's first row
     *
     * @param bool $process
     *
     * @return array|null
     */
    public function fetchRow($process = false)
    {
        //$this->db->clear();
        $query = $this->db->get($this->_name, 1, 0);
        if ($query->num_rows() == 0) {
            return null;
        }
        if ($process) {
            return $this->processRow($query->row_array());
        }

        return $query->row_array();
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function processRow(array $row)
    {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = htmlspecialchars($value);
            }
        }

        return $row;
    }

    /**
     * Returns data by PK list
     *
     * @param array $pks
     *
     * @return array
     * @throws \Exception
     */
    public function getByIds(array $pks)
    {
        if ($this->_primary === null) {
            throw new Exception('В таблице ' . $this->_name . ' не определён первичный ключ');
        }

        if (0 === count($pks)) {
            throw new Exception('Список первичных ключей пуст');
        }

        foreach ($pks as $pk) {
            if (count($this->_primary) != count($pk)) {
                throw new Exception(
                    'Количество полей первичного ключа не соответствует количество переданных параметров.'
                );
            }
        }

        if (1 === count($this->_primary)) {
            if (is_array($pks[0])) {
                $pks = array_map('reset', $pks);
            }
            $this->db->where_in($this->_primary[0], $pks);
        } else {
            foreach ($pks as $pk) {
                $this->db->or_group_start();
                for ($i = 0, $j = count($this->_primary); $i < $j; $i++) {
                    $this->db->where($this->_primary[$i], $pk[$i]);
                }
                $this->db->group_end();
            }
        }

        return $this->fetchAll();
    }

    /**
     * Returns query results
     *
     * @param bool $process
     *
     * @return array
     */
    public function fetchAll($process = false)
    {
        //$this->db->clear();
        $query = $this->db->get($this->_name);
        if ($process) {
            return array_map([
                $this,
                'processRow',
            ], $query->result_array());
        }

        return $query->result_array();
    }

    /**
     * Wrapper for insert
     *
     * @param array $set inserted array(s)
     *
     * @return int affected rows or insert id
     */
    public function insert(array $set)
    {
        $this->db->insert($this->_name, $set);
        if (is_array(current($set))) {
            return $this->db->affected_rows();
        }

        return $this->db->insert_id();
    }

    /**
     * Wrapper for update
     *
     * @param array $set updated array(s)
     *
     * @return int affected rows or insert id
     */
    public function replace(array $set)
    {
        $this->db->replace($this->_name, $set);
        if (is_array(current($set))) {
            return $this->db->affected_rows();
        }

        return $this->db->insert_id();
    }

    /**
     * Update by PK
     *
     * @param mixed|null $pk  PK
     * @param array      $set updated array
     *
     * @return int affected rows
     * @throws \Exception
     */
    public function update($pk, array $set)
    {
        if ($this->_primary === null) {
            throw new Exception('В таблице ' . $this->_name . ' не определён первичный ключ');
        }

        if ($pk !== null) {
            if (!is_array($pk)) {
                $pk = [$pk];
            }
            if (count($this->_primary) != count($pk)) {
                throw new Exception(
                    'Количество полей первичного ключа не соответствует количество переданных параметров.'
                );
            }

            for ($i = 0, $j = count($this->_primary); $i < $j; $i++) {
                $this->db->where($this->_primary[$i], $pk[$i]);
            }
        }

        $this->db->update($this->_name, $set);

        return $this->db->affected_rows();
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        } elseif (method_exists($this->db, $name)) {
            call_user_func_array([$this->db, $name], $arguments);
        }

        return $this;
    }

}
