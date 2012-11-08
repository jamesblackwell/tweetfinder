<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The homepage controller
 * @author James Blackwell
 */
class Links extends CI_Controller {
    
    
    /**
     * Accecpts the links from the user via POST and sends to model for processing
     * Returns the result via JSON
     * 
     * @return Json
     * @author James Blackwell
     */
    public function process_links()
    {
        $data = array();
        //simple check to see if any links are present
        if (strlen($this->input->post('links_to_check')) > 5)
        {
            $this->load->model('Links_model');
            $data['result'] = $this->Links_model->process_links($this->input->post('links_to_check'));
        }
        
        
        //test
        //$this->load->model('Links_model');
        //$data['result'] = $this->Links_model->process_links('http://www.alessiomadeyski.com/why-i-stopped-meet-your-seo/');
        
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    //------------------------------------------------------------------
   
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */