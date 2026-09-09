<?php
// =============================================
// BazarDor — Database Configuration
// =============================================
// IMPORTANT: Update these values to match your shared hosting MySQL credentials.

define('DB_HOST', 'localhost');
define('DB_NAME', 'bazardo1_bazardor_db');
define('DB_USER', 'bazardo1_bazardor_db');
define('DB_PASS', 'M-U;Zi9t?cuq0;13');

// Site configuration
define('SITE_NAME', 'নিত্যদিন বাজার');
define('SITE_URL', 'https://bazardor.app');
define('SITE_DESCRIPTION', 'বাংলাদেশের ৬৪ জেলার দৈনন্দিন বাজারদর, তাজা সবজি, মাছ, মাংস ও মশলার আজকের সঠিক দাম জানুন এক ক্লিকেই। বাজারদরের নির্ভরযোগ্য তথ্য দিয়ে আপনার দৈনন্দিন কেনাকাটাকে করুন আরও সহজ ও সাশ্রয়ী।');

// Session config
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
