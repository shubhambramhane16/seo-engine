<?php

namespace App\Helpers;

class PGHelpers
{
    public static function UpdateTransactionData($data, $array)
    {
        if ((isset($data['txnid']) && !empty($data['txnid'])) || (isset($data['TxnId']) && !empty($data['TxnId']))) {
            $data['txnid'] = isset($data['txnid']) ? $data['txnid'] : $data['TxnId'];
            $details = \App\Models\PaymentTransaction::UpdateOrCreate(['payment_request_id' => $data['txnid']], $array);
            return $details;
        }
    }
    public static function GeneratePaymentTransactionLog($array)
    {
        return \App\Models\PaymentTransactionLog::UpdateOrCreate(['id' => null], $array);
    }
    public static function GetPaymentTransaction($txnId)
    {
        return \App\Models\PaymentTransaction::where('payment_request_id', $txnId)->first();
    }
    public static function generateLog($method, $text)
    {
        try {
            $logMessage = json_encode($text);
            // The path to the log file

            // Get the current timestamp
            $timestamp = date("Y-m-d H:i:s");
            $timestamp2 = date("Y-m-d-H");
            $filename = "log_$timestamp2.txt";
            // Create the log entry with timestamp
            $logEntry = "[$timestamp] - $method \n $logMessage" . PHP_EOL;
            // Use file_put_contents to append the log entry to the file
            // file_put_contents('/var/www/html/admin/preprod/'.$filename, $logEntry, FILE_APPEND);
            $file = fopen(public_path() . '/' . 'logs/' . $filename, "a");
            // Check if the log entry was written successfully
            fwrite($file, $logEntry);
            fclose($file);
        } catch (Exception $error) {
        }
    }
}
