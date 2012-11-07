<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The about controller
 * @author James Blackwell
 */
class About extends CI_Controller {
    
    /**
     * The about page
     * 
     * @author James Blackwell
     */
    public function index()
    {
        $data['title'] = 'TweetFinder - About Twitter Username Finder';
        $data['content'] = 'about/about';     
        $this->load->view('template', $data);
    }
    
    //------------------------------------------------------------------
   
}

/* End of file about.php */
/* Location: ./application/controllers/about.php */