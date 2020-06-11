<?php  

if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Layout
{  
        protected $CI;
        protected $layout;

    public function __construct(){
         $this->CI =& get_instance();
         $this->layout = 'layout/layout';
    }     

    //function Layout($layout = "layout/layout")
    //{
    //    $this->layout = $layout;
    //}

    function setLayout($layout)
    {
      $this->layout = $layout;
    }  
    function view($view, $data=null, $return=false)
    {
        $loadedData = array();
        $loadedData['content_for_layout'] = $this->CI->load->view($view,$data,true);   

        if($return)
        {
            $output = $this->CI->load->view($this->layout, $loadedData, true);
            return $output;

        }
        else
        {
            $this->CI->load->view($this->layout, $loadedData, false);
        }
    }
}

?>