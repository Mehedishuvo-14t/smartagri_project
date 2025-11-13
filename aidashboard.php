<?php
session_start();
include 'config.php';

define('OPENAI_API_KEY', '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'); 

function callGPT5Nano($prompt){
    $url = "https://api.openai.com/v1/responses";
    $data = [
        "model" => "gpt-5-nano",
        "input" => $prompt,
        "store" => true,
        "max_output_tokens" => 40  // short answer
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . OPENAI_API_KEY,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // SSL ignore for testing only
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    if($response === false){
        return "cURL Error: ".curl_error($ch);
    }

    $res = json_decode($response,true);

    if(isset($res['output'][0]['content'][0]['text'])){
        return $res['output'][0]['content'][0]['text'];
    }

    return json_encode($res); // debug
}

// Detect if input is Bangla
function isBangla($text){
    return preg_match('/[অ-ঔক-হ]/u', $text);
}

// Process user input
$ai_response = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_text = trim($_POST['ai_input'] ?? '');
    if ($input_text !== '') {
        if(isBangla($input_text)){
            $prompt = "Answer briefly in Bangla: " . $input_text;
        } else {
            $prompt = "Answer briefly in English: " . $input_text;
        }

        $ai_response = callGPT5Nano($prompt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Smart Agriculture Chatbot</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
.container { max-width: 700px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
textarea { width: 100%; height: 120px; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; resize: vertical; font-size: 14px; }
button.submit-btn { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; }
.ai-response { background: #f1f1f1; padding: 15px; border-radius: 5px; margin-top: 20px; white-space: pre-wrap; }
</style>
</head>
<body>
<header>
    <h1>Smart Agriculture Chatbot</h1>
</header>

<div class="container">
    <form method="post">
        <textarea name="ai_input" placeholder="আপনার প্রশ্ন লিখুন..."><?php echo htmlspecialchars($_POST['ai_input'] ?? ''); ?></textarea>
        <button type="submit" class="submit-btn">Submit</button>
    </form>

    <?php if($ai_response): ?>
    <div class="ai-response">
        <p><?php echo nl2br(htmlspecialchars($ai_response)); ?></p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
