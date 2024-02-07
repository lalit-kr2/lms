<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
/**
 * @package   plugintype_pluginname
 * @copyright 2020, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    
class test_table extends table_sql {
    public function __construct($uniqeuid) {
        parent::__construct($uniqeuid);
        $columns = array("sr","name", "logo","timemodified","action");
        $this->define_columns($columns);
        $headers = array("Sno","Name", "logo" ,"Time Modified","Action");
        $this->define_headers($headers);
        $this->no_sorting("sr");
        $this->no_sorting("name");
        $this->no_sorting("state");
        $this->no_sorting("logo");
        $this->no_sorting("timemodified");
        $this->no_sorting("action");
    }


    public function col_name($values){
        global $CFG,$DB;
        if($values->state==1){
            $gateway= $DB->get_record('new_payment_gateways', ['id' => $values->id]);
            return $values->name." "."<span class='heading1'> Primary</span>";
        }else{
            return $values->name;
        }
    }


    public function col_action($values){
        global $CFG,$DB;
        $gateway= $DB->get_record('new_payment_gateways', ['id' => $values->id]);
        $action="
        <a class='btn btn-secondary' href = '$CFG->wwwroot/local/payment/add_gateway.php?id=$values->id' title='Edit'><i class='fa fa-pencil'></i></a>
        <a class='btn btn-secondary' href = '$CFG->wwwroot/local/payment/gateways.php?action=delete&id=$values->id' title='Delete'><i class='fa fa-trash'></i></a>
        ";
        if($values->state!=1){
            $action.="
            <a class='btn btn-primary' href = '$CFG->wwwroot/local/payment/gateways.php?id=$values->id' title='Set Primary'>Set Primary</a>
            ";
        }
        return $action;
      
    }


  

    function col_sr($values) {
        global $page, $a,$sr;
        static $start = 1;
        if ($page == 0) {
            if($a==0){
                $a++;
            }
            return $a++;
        }else{
            $a++;
            $sr = $a + ($page * 10);
            return $sr;

        }
    }

    function col_logo($values) {
        $action="<img src = '$values->logo' width='50px' height='50px' title='Logo'></img>";
        return $action;
    }

    
    public function col_timemodified($values){
        if($values->timemodified==0){
            return "Never";
        }else{
            return date('d M Y', $values->timemodified);
        }
    }
}



