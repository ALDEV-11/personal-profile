<?php
/**
 * Logout Handler
 * Menghapus session dan redirect ke login page
 */

require_once '../database/config.php';

// Logout user
logoutUser();
