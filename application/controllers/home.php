<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The homepage controller
 * @author James Blackwell
 */
class Home extends CI_Controller {
    
    /**
     * The home page
     * 
     * @author James Blackwell
     */
	public function index()
	{
	    $data['title'] = 'TweetFinder - Find Twitter Usernames From URLs';
	    $data['content'] = 'home/home';     
		$this->load->view('template', $data);
	}
    
    //------------------------------------------------------------------
   
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */