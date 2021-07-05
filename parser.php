<?php
// global array to collect all file paths after all iterations
$allFilePathsArray = [];

function fileParser($inputPath) {
    // open file and read each line into an array
    $fileArray = file($inputPath, FILE_IGNORE_NEW_LINES);

    // gets the file name portion of the file path
    $fileName = basename($inputPath);
    // gets the directory name portion of the file path
    $fileDirName = dirname($inputPath);

    // initialize array to collect the lines with file names
    $fileNameArray= [];
    // initialize array to collect the lines with numbers
    $fileNumberArray = [];

    // loop through the opened file to separate numbers from file names
    foreach($fileArray as $line => $val) {
        if (is_numeric($val)) {
            array_push($fileNumberArray, (int)$val);
        } else {
            array_push($fileNameArray, $val);
        }
    }

    // loop through the $fileNameArray and adds file names with at least one file name contained in it to the beginning of the $fileNameArray thus giving preference to file with file names contained in them
    foreach($fileNameArray as $fileNameKey => $fileNameVal) {
        foreach(file($fileNameVal, FILE_IGNORE_NEW_LINES) as $subFileNameKey => $subFileNameVal) {
            if (!is_numeric($subFileNameVal)) {
                array_unshift($fileNameArray, $fileNameVal);
                break;
            }
        }
    }

    // removes duplicates from the $fileNameArray since there will be repetitions when file names with file names included in them are added to the beginning of the $fileNameArray
    array_unique($fileNameArray);

    // goes over the loop again to open file names contained within the current open file
    foreach($fileNameArray as $filePath => $val) {
        fileParser($fileDirName . "/" . $val);
    }

    // assigns the sum of all numbers in a file as values to file name in the $allFilePathsArray
    $GLOBALS['allFilePathsArray'][$fileName] = array_sum($fileNumberArray);

}

function fileNumberAdder() {
    // loop through the file and sums up all number in subfiles up to the file itself
    foreach($GLOBALS['allFilePathsArray'] as $x => $xVal) {
        foreach($GLOBALS['allFilePathsArray'] as $y => $yVal) {
            if (in_array($y, file($x, FILE_IGNORE_NEW_LINES))) {
                $xVal += $yVal;
                $GLOBALS['allFilePathsArray'][$x] = $xVal;
            }
        }

    }
}

function formatPrinter() {
    // print out the file names with the sum of sub files up to the file level in desired format
    foreach(array_reverse($GLOBALS['allFilePathsArray']) as $allFilePathsKey => $allFilePathsVal) {
        echo "$allFilePathsKey - $allFilePathsVal" . "<br>";
    }
}

// Start here...
// Pass the name of the file to be parsed with the directory appended
fileParser("c:/xampp/htdocs/ltk/a.txt");

fileNumberAdder();

formatPrinter();

