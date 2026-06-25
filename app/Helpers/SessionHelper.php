<?php

if (!function_exists('check_session')) {
    function check_session() {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('logged_in') || !$session->get('user_id')) {
            // Set flash message for user feedback
            $session->setFlashdata('error', 'Your session has expired. Please login again.');
            
            // Redirect to login page
            return redirect()->to('/manufacturingerp/login');
        }
        
        return true;
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        return check_session();
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        $session = session();
        return $session->get('logged_in') && $session->get('user_id');
    }
}
