<?php

$path = "./documents/";
$files = scandir($path);

echo "----------------------------------\n";
echo "Files list: " . "\n";
$mask = "|%5.5s |%-30.30s \n";
for ($i = 2; $i < count($files); $i++) {
    printf($mask, "[$i]", $files[$i]);
}

echo "Select file: ";
$index = (int)getInput();
$file_name = $files[$index];

$headers = null;

$lines = [];
$output = [];

if ($file = fopen($path . $file_name, "r")) {

    $line = fgetcsv($file, 10000, ",");

    displayFields($line) . "\n";

    echo "\n\n Enter indexes to use (comma separated): " . "\n";
    $fields_to_use = explode(",", getInput());

    echo "\n\n Enter new column names (comma separated): " . "\n";
    $fields_name = explode(",", getInput());

    echo "\n Enter filename to save data: " . "\n";
    $output_file = getInput();

    if (!$headers) {
        $headers = $line;
    }

    $line_count = 0;
    
    while (($line = fgetcsv($file, 10000, ",")) !== false) {
        $lines[] = array_combine($headers, $line);

        $line_count++;
    }

    echo "Gathering data from $file_name... \n\n";

    try {
        for ($i = 0; $i < count($lines); $i++) {
            $row = $lines[$i];

            $new_row_val = [];

            foreach ($fields_to_use as $key) {
                $key = $key;
                $index = $headers[$key];
                $new_row_val[] = $row[$index] . "\n";
            }

            $output[] = array_combine($fields_name, $new_row_val);
        }
    } catch (Exception $e) {
        echo $e->getMessage() . "\n"; 
    }

    sleep(10);

    echo "Logs: \n";
    echo "Total lines gathered from document " . $line_count . " \n";
    echo "Total gathered data " . count($output) . " \n";

    echo "Transferring gathered data to output.csv...\n";
    createCSV($output_file, $output);
    echo "End Log: \n\n";

    sleep(10);
    echo "Data transfer complete \n";
    fclose($file);
    echo "----------------------------------";
}

// Input getter
function getInput() {
    return trim(fgets(fopen("php://stdin", 'r')));
}

/*
 * Display columns from selected document
 * @param Array
 * @return Void
 */
function displayFields($arr) {
    $count = 0;

    foreach ($arr as $key => $field) {
        $str = "$key" . $field . " "; 

        if ($count == 6) {
            echo " | $key - $field \n\n";
            $count = 0;
        } else {
            echo " | $key - $field";
        }

        $count++;
    }
}

/*
 * Transfer Gathered data from Selected document 
 * @param String 
 * @param Array
 * @param Void
 */
function createCSV($file_name, $data) {
    $file = fopen('./outputs/' . $file_name, 'w');
    $headers = array_keys($data[0]);

    fputcsv($file, $headers);

    $transfer_count = 0;

    for ($i = 0; $i < count($data); $i++) {
        $values = array_values($data[$i]);
        fputcsv($file, trim($values));
        $transfer_count++;
    }

    echo "Total of $transfer_count transferred data to $file_name \n";

    fclose($file);
}
