<?php
header('Content-Type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $websites = $_POST['website'];
    $chunks = array_chunk($websites, 2);
    $results = [];
    foreach ($chunks as $website) {
        $url = "https://www.dapachecker.org/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, true); 
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $results[] = ['error' => 'Curl error: ' . curl_error($ch)];
            continue; // Skip to the next iteration of the loops
        }
        curl_close($ch);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = '';
        foreach ($matches[1] as $cookie) {
            parse_str($cookie, $cookieArray);
            foreach ($cookieArray as $key => $value) {
                $cookies .= $key . '=' . $value . ';'; 
            }
        }
        $cookies = rtrim($cookies); 
        if (preg_match('/<meta\s+name="_token"\s+content="([^"]*)"/i', $response, $tokenMatch)) {
            $token = $tokenMatch[1];
        } else {
            $token = "Token not found";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.dapachecker.org/checkDA_new');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $postFields = '';
        foreach ($website as $value) {
            $postFields .= '&links%5B%5D=' . urlencode($value);
        }
        $postFields .= '&selected_option=dapa';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'X-Csrf-Token: '.$token;
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'Cookie: '.$cookies;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $results[] = ['error' => 'Curl error: ' . curl_error($ch)];
            continue; // Skip to the next iteration of the loop
        }
        curl_close($ch);
        $results[] = json_decode($result, true);
    }
    echo json_encode($results);
    exit();
}
?>
