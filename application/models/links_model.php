<?php
if ( ! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Links model
 *
 * @author James Blackwell
 */
class Links_model extends CI_Model {
    
    /**
     * Todays date, set in constructor
     * 
     * @var string
     */
    public $today;
    /**
     * Current timestamp for mysql
     * 
     * @var string
     */
    public $now;

    public function __construct()
    {
        parent::__construct();
        $this->today = date('Y-m-d');
        $this->now = date('Y-m-d H:i:s');
    }

    //------------------------------------------------------------------------------

    /**
     * Loop through the links, sending each one to the library for checking
     * 
     * @param string
     * @return array
     * @author James Blackwell
     */
    public function process_links($links)
    {
        //split the links string by new line
        $links = explode("\n", $links);
        
        if (count($links) > 50)
            return 'max_links';
        
        $this->load->library('twitter_finder');
        $result = array();
        
        foreach ($links as $key => $link) 
        {
            $result[$key] = $this->twitter_finder->fetch($link);
            $result[$key]['original_link'] = $link;
        }
        return $result;
    }
    
    //------------------------------------------------------------------------------


}
