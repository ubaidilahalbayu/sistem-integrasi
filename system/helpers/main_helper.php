<?php
function view($file, $data = [], $ajax = false){
    $CI =& get_instance();
    if (is_bool($data)) {
        $ajax = $data;
        $data = [];
    }
    if ($ajax) {
        if (!$CI->input->is_ajax_request()) {
            show_404('Page Not Found');
            return;
        }
        try {
            ob_start();
            $CI->load->view($file, $data);
            $html_content = ob_get_clean();
            
            $response = [
                'status' => true,
                'html' => $html_content,
                'data' => $data,
                'message' => 'View loaded successfully'
            ];
        } catch (Exception $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }else{
        return $CI->load->view($file, $data);
    }
}