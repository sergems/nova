<?php
$seoPages = [
    'index.php' => [
        'title' => 'Nova Debt Counselling | Professional Debt Solutions',
        'description' => 'Explore professional debt counselling and debt review solutions from Nova Debt Counselling.'
    ],
    'about.php' => [
        'title' => 'About Nova Debt Counselling',
        'description' => 'Learn about Nova Debt Counselling, our mission, and our approach to helping clients regain financial control.'
    ],
    'services.php' => [
        'title' => 'Debt Counselling Services | Nova',
        'description' => 'Review Nova Debt Counselling services, including debt review cancellation and related support.'
    ],
    'contact.php' => [
        'title' => 'Contact Nova Debt Counselling',
        'description' => 'Contact Nova Debt Counselling to request a confidential consultation.'
    ],
    'thank-you.php' => [
        'title' => 'Thank You | Nova Debt Counselling',
        'description' => 'Thank you for contacting Nova Debt Counselling.'
    ]
];

$currentFile = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$pageSeo = $seoPages[$currentFile] ?? [
    'title' => 'Nova Debt Counselling',
    'description' => 'Professional debt counselling services from Nova Debt Counselling.'
];
$pageTitle = $pageSeo['title'];
$pageDescription = $pageSeo['description'];
