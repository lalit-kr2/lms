<?php
namespace local_payment;
defined('MOODLE_INTERNAL') || die();

    
        class observers {
        public static function handlee(\core\event\course_viewed $event) {
        // Add your custom code here
            global $DB,$CFG,$USER;

            // echo '<style> 
            // body{
            //     display: none;
            // }
            // </style>';
            // echo '<script>
            // window.location.replace( "'.$CFG->wwwroot.'/local/course_view/course_policy.php");
            // </script>';
        }
}
    

