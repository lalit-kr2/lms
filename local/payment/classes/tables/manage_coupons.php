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
        $columns = array("sr","coursename","coupon","type","discount","maxusage","duration","timecreated");
        $this->define_columns($columns);
        $headers = array("Sno","Course","Coupon Code" ,"Type", "Discount","Maxusage","Duration","Timecreated");
        $this->define_headers($headers);
        $this->no_sorting("sr");
        $this->no_sorting("coursename");
        $this->no_sorting("coupon");
        $this->no_sorting("discount");
        $this->no_sorting("type");
        $this->no_sorting("maxusage");
        $this->no_sorting("duration");
        $this->no_sorting("timecreated");
    }


    // public function col_action($values){
    //     global $CFG,$DB;
    //     // var_dump($values->relateduserid);
    //     // die;
    //     $user= $DB->get_record('user', ['id' => $values->userid]);
    //     $action="<a href = '$CFG->wwwroot/user/editadvanced.php?id=$values->userid' title='Edit'>
    //     <i class='fa fa-pencil'></i></a>
    //     <a data-userid='$values->userid' class='delete-data' id='delete' title='Delete'>
    //     <i class='fa fa-trash'></i></a>";

    //     if($user->suspended==0){
    //         $action.=" <a data-userid='$values->userid' class='suspend-data' id='suspend' title='Suspend'>
    //         <i class='fa fa-eye' ></i></a>";
    //     }
    //     else{
    //         $action.=" <a data-userid='$values->userid' class='suspend-data' id='suspend' title='Unsuspend'>
    //         <i class='fa fa-eye-slash' ></i></a>";
    //     }  
    //     return $action;
    // }


    // public function col_lastaccess($values){
    //     if($values->lastaccess==0){
    //         return "Never";
    //     }else{
    //         return date('d M Y', $values->lastaccess);
    //     }
    // }

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

    public function col_duration($values){
        $duration=date('d M Y', $values->start_date).' to '.date('d M Y', $values->expiry_date);
        return $duration;
    }


    public function col_timecreated($values){
        if($values->timecreated==0){
            return "Never";
        }else{
            return date('d M Y', $values->timecreated);
        }
    }

  

}



