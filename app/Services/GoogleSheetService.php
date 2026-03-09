<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId = '1OtBbxjFKxmfYUJVliHm1Vi1qsfO10Pc4CulUl7RbKZQ'; // Your sheet ID

    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Laravel Google Sheets Sync');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        // $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $this->service = new Google_Service_Sheets($client);
    }

    /**
     * Append a new order row
     */
    public function appendOrder(array $orderData)
    {
        $range = 'CRM Order Tracker!A1';
        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$orderData]
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        try {
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $body,
                $params
            );
        } catch (\Exception $e) {
            Log::error('Error appending data to Google Sheet: ' . $e->getMessage());
        }
    }

    /**
     * Update a row's columns in Google Sheets by matching Order # (column B)
     * $updates = [ 'Status' => 'Delivered', 'Date Delivered' => '2025-06-18', ... ]
     * Keys are column headers exactly matching your sheet
     */
    public function updateOrderByOrderNumber(string $orderNumber, array $updates)
    {
        try {
            //Read header row (row 1)
            $headerRange = 'CRM Order Tracker!A1:ZZ1'; // Assuming headers in row 1
            $headerResponse = $this->service->spreadsheets_values->get($this->spreadsheetId, $headerRange);
            $headers = $headerResponse->getValues()[0] ?? [];

            //Read data rows starting from row 2
            $dataRange = 'CRM Order Tracker!A2:ZZ'; // Adjust as needed
            $dataResponse = $this->service->spreadsheets_values->get($this->spreadsheetId, $dataRange);
            $rows = $dataResponse->getValues();

            if (!$rows) {
                Log::info('No rows found in Google Sheet.');
                return false;
            }

            //Find row with matching Order #
            foreach ($rows as $rowIndex => $row) {
                if (isset($row[1]) && trim($row[1]) === trim($orderNumber)) {
                    //Found matching row
                    $sheetUpdates = [];

                    //Prepare updates per column letter
                    foreach ($updates as $colName => $value) {
                        $colIndex = array_search(strtolower($colName), array_map('strtolower', $headers));
                        if ($colIndex === false) {
                            //Column not found, skip
                            continue;
                        }
                        $cell = $this->getColumnLetter($colIndex) . ($rowIndex + 2); // +2 because data starts at row 2
                        $sheetUpdates[] = [
                            'range' => "CRM Order Tracker!$cell",
                            'values' => [[$value]],
                        ];
                    }

                    if (empty($sheetUpdates)) {
                        Log::info('No valid columns found to update in Google Sheet.');
                        return false;
                    }

                    $body = new \Google_Service_Sheets_BatchUpdateValuesRequest([
                        'valueInputOption' => 'USER_ENTERED',
                        'data' => $sheetUpdates,
                    ]);

                    $this->service->spreadsheets_values->batchUpdate($this->spreadsheetId, $body);

                    return true;
                }
            }

            //Order # not found
            Log::info("Order # $orderNumber not found in Google Sheet.");
            return false;

        } catch (\Exception $e) {
            Log::error('Error updating order in Google Sheet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert zero-based column index to column letter(s)
     * 0 => A, 1 => B, ..., 25 => Z, 26 => AA ...
     */
    private function getColumnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }
    
}
