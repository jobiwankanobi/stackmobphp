<?php

/**
 * Methods for querying Stackmob
 *
 * @author jobiwankanobi
 */

namespace Stackmob;

class Query extends Object {

    protected $_pk = null;
    protected $_where = array();
    protected $_select = array();
    protected $_orderby = array();
    protected $_depth = null;
    protected $_rest = null;
    protected $_range = null;
 

    /**
     * 
     * @param type $objectClass
     * @param type $pk
     */
    public function __construct($objectClass, $pk = null){
        $this->log = new DummyLogger();

        $this->objectClass = $objectClass;
        $this->_pk = $pk ? $pk : strtolower($objectClass) . '_id';

        $this->_rest = new \Stackmob\Rest();

    }
 
    /**
     * https://developer.stackmob.com/sdks/rest/api#a-equality_query
     * 
     * @param type $key
     * @param type $value
     */
    public function isEqual($key, $value) {
            $this->_where[$key] = $value;
    }
	
    /**
     * https://developer.stackmob.com/sdks/rest/api#a-inequality_queries____________________null_
     * 
     * @param type $key
     * @param type $value
     */
    public function notEqual($key, $value) {
            $this->setWhereKeyHashValue($key, 'ne', $value);
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-inequality_queries____________________null_
     * 
     * @param type $key
     * @param type $value
     */
    public function greaterThan($key, $value) { 
            $this->setWhereKeyHashValue($key, 'gt', $value);
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-inequality_queries____________________null_
     * 
     * @param type $key
     * @param type $value
     */
    public function lessThan($key, $value) { 
            $this->setWhereKeyHashValue($key, 'lt', $value);
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-equality_query
     * 
     * @param type $key
     */
    public function notNull($key) {
            $this->setWhereKeyHashValue($key, 'null', 'false');
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-equality_query
     * 
     * @param type $key
     */
    public function isNull($key) {
            $this->setWhereKeyHashValue($key, 'null', 'true');
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-querying_for_multiple_values
     * https://developer.stackmob.com/sdks/rest/api#a-querying_arrays
     * 
     * @param type $key
     * @param type $values
     */
    public function in($key, $values) {
            $this->setWhereKeyHashValue($key, 'in', implode(',', $values));
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-get_-_expanding_relationships:_get_full_objects__not_just_ids
     * 
     * @param type $value
     */
    public function depth($value) {
            $this->_depth = $value;
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-selecting_fields_to_return
     * 
     * @param type $values
     */
    public function select($values=array()) {
            $this->_select = $values;
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-order_by
     * 
     * @param type $values
     */
    public function asc($values=array()) {
            $this->_orderby['asc'] = $values;
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-order_by
     * 
     * @param type $values
     */
    public function desc($values=array()) {
            $this->_orderby['desc'] = $values;
    }
	
    /**
     * Pagination
     * 
     * https://developer.stackmob.com/sdks/rest/api#a-pagination
     * 
     * @param type $low
     * @param type $high
     * @return boolean
     */
    public function range($low, $high) {
        if($low && $high && ($low < $high)) {
            $this->_range = "Range: objects=$low-$high";
        } else {
            return false;
        }
        
        return true;
    }
	
    /**
     * Limit
     * 
     * https://developer.stackmob.com/sdks/rest/api#a-pagination
     * 
     * @param type $max
     */
    public function limit($max) {
        return $this->range(0, $max);
    }
    
    
    /**
     * Retrieves a list of Stackmob Objects that satisfy this query.
     *
     * @return array
     */
    public function find(){

        if($this->_depth) {
            $this->_where[] = array("_expand" => $this->_depth);
        }

        $params = $this->_where;
        $selects = $this->preparedSelects();
        $order = $this->preparedOrderBy();
        $range = $this->_range;
        $depth = $this->_depth;
        $objects = array();

        $found = $this->_find($params,$selects,$order,$range,$depth);

        if($this->_rest->statusCode() == 200){
            $this->_count = $this->_rest->count();
            $indexKey = $this->indexKey;
            if(!is_array($found))
                return array();
            foreach($found as $attributes){
                if($indexKey){
                    $index = isset($attributes->$indexKey) ? $attributes->$indexKey : count($objects);
                }else{
                    $index = count($objects);
                }

                if($this->objectClass == Object::USER_OBJECT_CLASS){
                    $objects[$index] = new \Stackmob\User($attributes);
                }else{
                    $objects[$index] = new \Stackmob\Object($this->objectClass,$attributes);
                }
            }
        }

        return $objects;
    }

   protected function _find($params,$selects=null,$order=null,$range=null,$depth=null){

        if($this->objectClass == \Stackmob\Object::USER_OBJECT_CLASS){
            $found = $this->_rest->getUsers($params,$selects,$order,$range,$depth);
        }else{
            $found = $this->_rest->getObjects($this->objectClass,$params,$selects,$order,$range,$depth);
        }

        return $found;
    }

    /**
     * 
     * @return string
     */
    protected function preparedOrderBy() {
        $order = null;
        if(!empty($this->_orderBy)){
            $order = array();
            foreach($this->_orderBy['asc'] as $item) {
                $order[] = "$item:asc";
            }
            foreach($this->_orderBy['desc'] as $item) {
                $order[] = "$item:desc";
            }
            
            $order = "X-StackMob-OrderBy:" . implode(',',$order);
        }
        
        return $order;
    }
    
    /**
     * 
     * @return string
     */
    protected function preparedSelects() {
        $select = null;
        if(!empty($this->_select)){
            $select = "X-StackMob-Select:" . implode(',',$this->_select);
        }
        return $select;
    }



    /**
     * mutates where
     */
    protected function setWhereKeyHashValue($whereKey,$op,$value){
        // If equals value was defined, this will override it
        if(!isset($this->_where[$whereKey]) || !is_array($this->_where[$whereKey])){
            $this->_where[$whereKey] = array();
        }
        $this->_where[$whereKey][$op] = $value;
    }
}

?>