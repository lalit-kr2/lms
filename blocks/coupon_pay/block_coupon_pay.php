<?php
class block_coupon_pay extends block_base
{
    public function init()
    {
        $this->title = get_string('pluginname', 'block_coupon_pay');
    }

    public function get_content()
    {
        global $CFG;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = "<a href='$CFG->wwwroot/blocks/coupon_pay/index.php' class='button'>Create Coupon</a>";
        return $this->content;
    }

    public function specialization()
    {
        if (isset($this->config)) {
            $this->title = $this->config->buttonlabel;
        }
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => false,
            'course-view' => false,
            'mod' => false,
            'my' => true,
        ];
    }
}
