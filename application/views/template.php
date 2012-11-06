<?php
    $this->load->view('includes/header');
    
    $this->load->view('includes/nav');?>
    <div id="body_content">
        <?php $this->load->view($content); ?>
    </div>
    <?php $this->load->view('includes/footer');?>