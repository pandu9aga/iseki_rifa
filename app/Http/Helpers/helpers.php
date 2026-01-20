<?php
if (!function_exists('isEmployeeLoggedIn')) {
    function isEmployeeLoggedIn()
    {
        return session()->has('employee_login') && session('employee_login');
    }
}

if (!function_exists('getEmployeeUser')) {
    function getEmployeeUser()
    {
        return session()->get('employee_user');
    }
}