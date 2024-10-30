<?php
namespace App\Services;

class JpesaPayment implements PaymentService{

    public function pay($amount,$phone_number,$reference){
        // Retrieve the callback URL and API key from the environment variables
        $callback_url = config('payment.jpesa_callback');
        $api_key      = config('payment.jpesa_key'); // Example key, replace with your actual key

        // Prepare the XML data
        $DATA = '<?xml version="1.0" encoding="ISO-8859-1"?>
                <g7bill>
                    <_key_>' . $api_key . '</_key_>
                    <cmd>account</cmd>
                    <action>credit</action>
                    <pt>mm</pt>
                    <mobile>' . $this->formatPhone($phone_number) . '</mobile>
                    <amount>' . $amount . '</amount>
                    <callback>' . $callback_url . '</callback>
                    <tx>' . $reference . '</tx>
                    <description>' . $reference . ' Internet Subscription</description>
                </g7bill>';

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://my.jpesa.com/api/");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $DATA);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);

        // Execute the request
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // Check for errors
        if ($err) {
            return [
                'success' => false,
                'message' => 'Error: ' . $err,
            ];
        }

        // Decode the response
        $array = json_decode($response, true);

        // Return the response
        return [
            'success' => true,
            'data' => $array,
        ];
    }

    public function processCallback($payload){

    }

    private function formatPhone($phone_no){
        $ptn = "/^0/";  // Regex //Your input, perhaps $_POST['textbox'] or whatever
		$rpltxt = "256";  // Replacement string
		return preg_replace($ptn, $rpltxt, $phone_no);
    }

}