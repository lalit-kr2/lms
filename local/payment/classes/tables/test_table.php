<?php

/**
 * Test table class to be put in test_table.php of root of Moodle installation.
 *  for defining some custom column names and proccessing
 * Username and Password feilds using custom and other column methods.
 */

class test_table extends table_sql {

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        
        $columns = array('sno','fullname','coursename','value','status','couponid','amount','message','gatewayid','timecreated', );
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('S.No.','','Course','Price', 'Status','Coupon Used', 'Amount Paid','Message','Gateway', 'Paid On', );
        $this->define_headers($headers);
   
    }

    function col_amount($values) {

       return $values->amount/100;
    }

    function col_timecreated($values){

        $str="Date not set";
        if($values->timecreated == 0){
            return $str;
        }else{
            return date("d-m-Y", $values->timecreated);
        }
    }

    function col_couponid($values){
        if($values->couponid == 0){
            return "No";
        }else{
            return "Yes";
        }
    }

    function col_gatewayid($values){
        global $DB;
        if($data=$DB->get_record('new_payment_gateways',['id'=>$values->gatewayid])){
            return $data->name;
        }else{
            return "NA";
        }

    }
  


    
}