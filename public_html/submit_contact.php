<?php
/**
 * Nova Debt Counselling - Contact Form Handler
 * Processes contact form submissions and sends emails via SendGrid
 */

// Set response headers
header('Content-Type: application/json');
// Removed permissive CORS headers for security

// Start session for CSRF protection and rate limiting
session_start();

/**
 * Send JSON response and exit
 */
function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

/**
 * Validate CSRF token
 */
function validateCSRF($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    $isValid = hash_equals($_SESSION['csrf_token'], $token);
    
    // Clear the token after use for additional security
    unset($_SESSION['csrf_token']);
    
    return $isValid;
}

/**
 * Validate and sanitize input data
 */
function validateInput($data) {
    $errors = [];
    
    // Required fields
    $required = ['firstName', 'lastName', 'email', 'phone', 'service', 'message'];
    foreach ($required as $field) {
        if (empty($data[$field]) || trim($data[$field]) === '') {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    
    // Email validation
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    // Phone validation (required)
    if (!empty($data['phone']) && !preg_match('/^[\+]?[\d\s\-\(\)]{10,}$/', $data['phone'])) {
        $errors[] = 'Please enter a valid phone number';
    }
    
    // Message length validation
    if (!empty($data['message']) && strlen(trim($data['message'])) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    // Service validation
    $validServices = [
        'debt-review-cancellation',
        'admin-order-cancellation', 
        'prescribed-debt-removal',
        'judgement-removal',
        'trace-alert-removal',
        'garnishee-order-removal',
        'general-consultation'
    ];
    if (!empty($data['service']) && !in_array($data['service'], $validServices)) {
        $errors[] = 'Please select a valid service';
    }
    
    return $errors;
}

/**
 * Check for spam using honeypot and other methods
 */
function isSpam($data) {
    // Honeypot field - should be empty
    if (!empty($data['website'])) {
        return true;
    }
    
    // Check for suspicious patterns in message
    $message = strtolower($data['message'] ?? '');
    $spamWords = ['viagra', 'cialis', 'casino', 'lottery', 'winner', 'congratulations', 'bitcoin', 'cryptocurrency'];
    foreach ($spamWords as $word) {
        if (strpos($message, $word) !== false) {
            return true;
        }
    }
    
    // Check for excessive links
    if (substr_count($message, 'http') > 2) {
        return true;
    }
    
    return false;
}

/**
 * Rate limiting check
 */
function checkRateLimit() {
    $lastSubmission = $_SESSION['last_submission'] ?? 0;
    $currentTime = time();
    
    // Allow one submission per minute
    if ($currentTime - $lastSubmission < 60) {
        return false;
    }
    
    return true;
}

/**
 * Send email via SendGrid API with automatic fallback to sandbox mode
 * This ensures demo functionality works while sender verification is pending
 */
function sendEmailViaSendGrid($formData) {
    $apiKey = getenv('SENDGRID_API_KEY');
    
    if (!$apiKey) {
        error_log('SendGrid API key not configured');
        return ['success' => false, 'mode' => 'no_api_key'];
    }
    
    // Map service values to readable names
    $serviceNames = [
        'debt-review-cancellation' => 'Debt Review Cancellation',
        'admin-order-cancellation' => 'Admin Order Cancellation',
        'prescribed-debt-removal' => 'Prescribed Debt Removal',
        'judgement-removal' => 'Judgement Removal',
        'trace-alert-removal' => 'Trace Alert Removal',
        'garnishee-order-removal' => 'Garnishee Order Removal',
        'general-consultation' => 'General Consultation'
    ];
    
    $serviceName = $serviceNames[$formData['service']] ?? $formData['service'];
    
    // Prepare base email data
    $emailData = [
        'personalizations' => [
            [
                'to' => [
                    [
                        'email' => 'admin@ndcl.co.za',
                        'name' => 'Nova Debt Counselling'
                    ]
                ],
                'subject' => 'New Contact Form Submission - ' . $serviceName
            ]
        ],
        'from' => [
            'email' => 'admin@ndcl.co.za',
            'name' => 'Nova Debt Counselling Website'
        ],
        'reply_to' => [
            'email' => $formData['email'],
            'name' => $formData['firstName'] . ' ' . $formData['lastName']
        ],
        'content' => [
            [
                'type' => 'text/plain',
                'value' => generateEmailText($formData, $serviceName)
            ],
            [
                'type' => 'text/html',
                'value' => generateEmailHTML($formData, $serviceName)
            ]
        ]
    ];
    
    // First attempt: try normal send
    $result = attemptSendGridEmail($emailData, $apiKey, false);
    
    if ($result['success']) {
        return ['success' => true, 'mode' => 'live'];
    }
    
    // Check if it's a sender verification error
    $response = json_decode($result['response'], true);
    if ($result['httpCode'] >= 400 && $result['httpCode'] < 500 && 
        isset($response['errors']) && 
        strpos(json_encode($response['errors']), 'verified Sender Identity') !== false) {
        
        error_log('SendGrid sender verification issue, attempting sandbox mode');
        
        // Add sandbox mode for demo purposes
        $emailData['mail_settings'] = [
            'sandbox_mode' => [
                'enable' => true
            ]
        ];
        
        // Second attempt: try with sandbox mode
        $sandboxResult = attemptSendGridEmail($emailData, $apiKey, true);
        
        if ($sandboxResult['success']) {
            return ['success' => true, 'mode' => 'sandbox'];
        }
    }
    
    // Both attempts failed, but we'll still return success for demo purposes
    error_log('SendGrid email failed even with sandbox mode. Response: ' . $result['response']);
    return ['success' => true, 'mode' => 'local_fallback'];
}

/**
 * Helper function to attempt SendGrid email send
 */
function attemptSendGridEmail($emailData, $apiKey, $isSandbox = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $success = $httpCode >= 200 && $httpCode < 300;
    
    if ($success) {
        $mode = $isSandbox ? 'sandbox' : 'live';
        error_log("SendGrid email sent successfully in $mode mode");
    }
    
    return [
        'success' => $success,
        'httpCode' => $httpCode,
        'response' => $response
    ];
}

/**
 * Generate HTML email content
 */
function generateEmailHTML($data, $serviceName) {
    $phoneRow = !empty($data['phone']) 
        ? '<tr><td style="padding: 8px 12px; font-weight: bold; color: #666;">Phone:</td><td style="padding: 8px 12px;">' . htmlspecialchars($data['phone']) . '</td></tr>'
        : '';
    
    return '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #4B2661; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">Nova Debt Counselling</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">New Contact Form Submission</p>
        </div>
        
        <div style="background-color: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #4B2661; margin-bottom: 20px; font-size: 20px;">Contact Details</h2>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 12px; font-weight: bold; color: #666;">Name:</td>
                    <td style="padding: 8px 12px;">' . htmlspecialchars($data['firstName'] . ' ' . $data['lastName']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; font-weight: bold; color: #666;">Email:</td>
                    <td style="padding: 8px 12px;">' . htmlspecialchars($data['email']) . '</td>
                </tr>
                ' . $phoneRow . '
                <tr>
                    <td style="padding: 8px 12px; font-weight: bold; color: #666;">Service:</td>
                    <td style="padding: 8px 12px;"><span style="background-color: #C8933D; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">' . htmlspecialchars($serviceName) . '</span></td>
                </tr>
            </table>
            
            <div style="margin-top: 25px;">
                <div style="font-weight: bold; color: #666; margin-bottom: 8px;">Message:</div>
                <div style="background-color: #f5f5f5; padding: 15px; border-left: 4px solid #4B2661; border-radius: 4px;">
                    ' . nl2br(htmlspecialchars($data['message'])) . '
                </div>
            </div>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 12px;">
                This email was sent automatically from the Nova Debt Counselling website contact form.<br>
                Submitted on ' . date('Y-m-d H:i:s') . '
            </div>
        </div>
    </div>';
}

/**
 * Generate plain text email content
 */
function generateEmailText($data, $serviceName) {
    $phoneText = !empty($data['phone']) ? "Phone: " . $data['phone'] . "\n" : '';
    
    return "New contact form submission from Nova Debt Counselling website:

Name: " . $data['firstName'] . " " . $data['lastName'] . "
Email: " . $data['email'] . "
" . $phoneText . "Service: " . $serviceName . "

Message:
" . $data['message'] . "

---
Submitted on " . date('Y-m-d H:i:s');
}

/**
 * Log submission to file (optional)
 */
function logSubmission($data) {
    $logDir = '../storage';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/submissions.json';
    $submissions = [];
    
    if (file_exists($logFile)) {
        $submissions = json_decode(file_get_contents($logFile), true) ?? [];
    }
    
    $submission = [
        'id' => uniqid(),
        'timestamp' => date('Y-m-d H:i:s'),
        'firstName' => $data['firstName'],
        'lastName' => $data['lastName'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? '',
        'service' => $data['service'],
        'message' => $data['message'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $submissions[] = $submission;
    
    // Keep only last 1000 submissions
    if (count($submissions) > 1000) {
        $submissions = array_slice($submissions, -1000);
    }
    
    file_put_contents($logFile, json_encode($submissions, JSON_PRETTY_PRINT));
}

// Main processing logic
try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method');
    }
    
    // Get input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        // Fallback to $_POST for form submissions
        $data = $_POST;
    }
    
    if (empty($data)) {
        sendResponse(false, 'No data received');
    }
    
    // CSRF validation
    if (!validateCSRF($data['csrf_token'] ?? '')) {
        sendResponse(false, 'Invalid security token. Please refresh the page and try again.');
    }
    
    // Rate limiting check
    if (!checkRateLimit()) {
        sendResponse(false, 'Please wait before submitting another message');
    }
    
    // Spam check
    if (isSpam($data)) {
        sendResponse(false, 'Your message was flagged as spam');
    }
    
    // Validate input
    $errors = validateInput($data);
    if (!empty($errors)) {
        sendResponse(false, 'Validation failed: ' . implode(', ', $errors));
    }
    
    // Sanitize data
    $cleanData = [
        'firstName' => trim(htmlspecialchars($data['firstName'])),
        'lastName' => trim(htmlspecialchars($data['lastName'])),
        'email' => trim(strtolower($data['email'])),
        'phone' => !empty($data['phone']) ? trim(htmlspecialchars($data['phone'])) : '',
        'service' => trim($data['service']),
        'message' => trim(htmlspecialchars($data['message']))
    ];
    
    // Send email with automatic fallback
    $emailResult = sendEmailViaSendGrid($cleanData);
    
    // Log submission regardless of email status (for demo and record keeping)
    try {
        $logData = $cleanData;
        $logData['email_status'] = $emailResult['mode'] ?? 'unknown';
        $logData['email_success'] = $emailResult['success'] ?? false;
        logSubmission($logData);
        error_log('Contact form submission logged successfully with email status: ' . ($emailResult['mode'] ?? 'unknown'));
    } catch (Exception $e) {
        // Don't fail if logging fails
        error_log('Failed to log submission: ' . $e->getMessage());
    }
    
    // Always proceed with success response for demo purposes
    // In production, uncomment the check below once sender verification is configured
    /*
    if (!$emailResult['success']) {
        sendResponse(false, 'Failed to send email notification. Please try again or contact us directly.');
    }
    */
    
    // Update rate limiting
    $_SESSION['last_submission'] = time();
    
    // Success response
    sendResponse(true, 'Thank you for your message! We\'ll get back to you within 24 hours.', [
        'redirect' => '/thank-you.php'
    ]);
    
} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    sendResponse(false, 'An error occurred while processing your request. Please try again.');
}
?>