<?php
// Function to handle the bid request and generate a banner campaign response

// Sample campaign data
$campaigns = [
    [
        "campaignname" => "Test_Banner_13th-31st_march_Developer",
        "advertiser" => "TestGP",
        "code" => "118965F12BE33FB7E",
        "appid" => "20240313103027",
        "tld" => "https://adplaytechnology.com/",
        "creative_type" => "1",
        "creative_id" => 167629,
        "dimension" => "320x480",
        "price" => 0.1, // Bid price
        "bidtype" => "CPM",
        "image_url" => "https://example.com/image.png",
        "country" => "Bangladesh",
        "device_make" => "Android,iOS,Desktop"
    ]
    // Add more campaigns as needed
];

// Function to validate and parse the bid request JSON
function handleBidRequest($bidRequestJson) {
    $bidRequest = json_decode($bidRequestJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ["error" => "Invalid bid request JSON"];
    }

    // Validate essential fields
    if (!isset($bidRequest['imp'][0]['banner']['w'], $bidRequest['imp'][0]['banner']['h'], $bidRequest['device']['geo']['country'])) {
        return ["error" => "Missing essential fields"];
    }

    return $bidRequest;
}

// Function to select the appropriate campaign based on the bid request
function selectCampaign($bidRequest, $campaigns) {
    $selectedCampaign = null;
    $highestBid = 0;

    foreach ($campaigns as $campaign) {
        // Check country and device compatibility
        if ($campaign['country'] === $bidRequest['device']['geo']['country']) {
            // Compare bid prices and select the highest one
            if ($campaign['price'] > $highestBid) {
                $selectedCampaign = $campaign;
                $highestBid = $campaign['price'];
            }
        }
    }

    return $selectedCampaign;
}

// Function to generate the response JSON
function generateBannerResponse($selectedCampaign, $bidRequest) {
    if ($selectedCampaign === null) {
        return ["error" => "No matching campaign found"];
    }

    // Create the response JSON
    $response = [
        "id" => $bidRequest['id'],
        "seatbid" => [
            [
                "bid" => [
                    [
                        "impid" => $bidRequest['imp'][0]['id'],
                        "price" => $selectedCampaign['price'],
                        "adid" => $selectedCampaign['code'],
                        "nurl" => $selectedCampaign['image_url'],
                        "adm" => "<img src='{$selectedCampaign['image_url']}'/>",
                        "crid" => $selectedCampaign['creative_id'],
                        "w" => $bidRequest['imp'][0]['banner']['w'],
                        "h" => $bidRequest['imp'][0]['banner']['h']
                    ]
                ]
            ]
        ]
    ];

    return $response;
}

// Example bid request JSON (you will replace this with actual bid request data)
$bidRequestJson = '{
    "id": "myB92gUhMdC5DUxndq3yAg",
    "imp": [
        {
            "id": "1",
            "banner": {
                "w": 320,
                "h": 50
            }
        }
    ],
    "device": {
        "geo": {
            "country": "Bangladesh"
        }
    }
}';

// Step-by-step process
$bidRequest = handleBidRequest($bidRequestJson);

if (isset($bidRequest['error'])) {
    echo json_encode($bidRequest);
    exit;
}

$selectedCampaign = selectCampaign($bidRequest, $campaigns);
$response = generateBannerResponse($selectedCampaign, $bidRequest);

// Output the response
header('Content-Type: application/json');
echo json_encode($response);

?>

