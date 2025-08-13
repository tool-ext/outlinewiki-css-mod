<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow requests from any origin
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Expose-Headers: *');
header('Access-Control-Max-Age: 86400');    // Cache for 24 hours

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// Set content type and cache control
header('Content-Type: text/css');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Get the requested style file from URL parameter
$style = isset($_GET['style']) ? $_GET['style'] : '';

// Security: Make sure we only serve .css files and prevent directory traversal
if (empty($style) || !preg_match('/^[a-zA-Z0-9_-]+\.css$/', $style)) {
    http_response_code(400);
    echo "/* Invalid CSS file requested */";
    exit;
}

// Define the order of CSS files to load
$css_files = [
    '/styles/debug.css', // Load debug/base styles first
    $style         // Then load the requested style
];

// Initialize combined CSS content
$combined_css = '';

// Load and combine CSS files in order
foreach ($css_files as $file) {
    $css_file = __DIR__ . '/' . $file;
    if (file_exists($css_file)) {
        $content = file_get_contents($css_file);
        if ($content === false) {
            $combined_css .= "/* Error reading CSS file: {$file} */\n";
        } else {
            $combined_css .= "/* Start of {$file} */\n";
            $combined_css .= $content;
            $combined_css .= "\n/* End of {$file} */\n\n";
        }
    } else {
        $combined_css .= "/* CSS file '{$file}' not found */\n";
    }
}

// Output the combined CSS
echo $combined_css;
?> 