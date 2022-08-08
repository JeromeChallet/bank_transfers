<?php


class FinalResult {
    function results($f) {//$f is the final result
        $d = fopen($f, "r"); // open file for reading. r is for read
        $h = fgetcsv($d); // read header line
        $rcs = []; // array to hold result codes
        while(!feof($d)) { // check if the end of the file has been reached
            $r = fgetcsv($d); // read next line
            if(count($r) == 16) { // if line has 16 fields
                $amt = !$r[8] || $r[8] == "0" ? 0 : (float) $r[8]; // ternar operator to check if amount is 0 or not
                $ban = !$r[6] ? "Bank account number missing" : (int) $r[6];  // ternar operator to check if bank account number is missing or not
                $bac = !$r[2] ? "Bank branch code missing" : $r[2]; // ternar operator to check if bank branch code is missing or not
                $e2e = !$r[10] && !$r[11] ? "End to end id missing" : $r[10] . $r[11]; // ternary operator to check if end to end id is missing or not
                $rcd = [ // array to hold result code data
                    "amount" => 
                    [
                        "currency" => $h[0], // currency is the first field in the header line
                        "subunits" => (int) ($amt * 100) // convert to subunits
                    ],
                    "bank_account_name" => str_replace(" ", "_", strtolower($r[7])), // replace spaces with underscores
                    "bank_account_number" => $ban, // bank account number is the 6th field in the row
                    "bank_branch_code" => $bac, // bank branch code is the 3rd field in the row
                    "bank_code" => $r[0],// bank code is the 1st field in the row
                    "end_to_end_id" => $e2e,// concatenate the two fields
                ];
                $rcs[] = $rcd; // add record to array
            }
        }
        $rcs = array_filter($rcs);// remove empty arrays
        return [
            "filename" => basename($f),// get the filename from the path
            "document" => $d,// document is the file handle
            "failure_code" => $h[1],// failure code is the second field in the header line
            "failure_message" => $h[2],// failure message is the third field in the header line
            "records" => $rcs// records is the array of result code data
        ];
    }
}

?>
