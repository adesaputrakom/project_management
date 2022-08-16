<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Encryption Integer to string CodeIgniter 3.x
 *
 * @package     CodeIgniter
 * @category    Helper
 */
function encrypt($argv1) {
    $baseValue = $argv1;
    $jump = (( $baseValue * 1996 ) + 8) / 2;

    $baseToStr = "$jump";
    $baseStrToChr = str_split($baseToStr, 1);
    $encryptValue = "";

    $codeOne = array("w4Hy", "Fg4c", "as60", "D8cr", "WnT6", "kls0", "SnTT", "c008", "LSqP", "T6Ra");
    $codeBigOne = array("x", "j", "V", "p", "K", "d", "f", "Y", "5", "t");
    $codeSave = array("M", "c", "B", "n", "N", "m", "Q", "u", "X", "q");

    if ($jump < 10) {
        for ($i = 0; $i < 10; $i++) {
            if ($jump == $i) {
                $encryptValue = $codeOne[$i];
                break;
            }
        }
    } else {
        $end = count($baseStrToChr);
        $save = 0;
        // echo $end."<br>";
        for ($i = 0; $i < $end; $i++) {
            for ($j = 0; $j < 10; $j++) {
                $tmp = intval($baseStrToChr[$i]);
                if ($tmp == $j) {
                    if (empty($encryptValue)) {
                        $encryptValue = $encryptValue . $codeBigOne[$j];
                        break;
                    } else {
                        $tmpp = str_split($encryptValue, 1);
                        $tmpcount = count($tmpp);
                        if ($tmpp[$tmpcount - 1] == $codeBigOne[$j]) {
                            $encryptValue = $encryptValue . $codeSave[$save];
                            $save++;
                            break;
                        } else {
                            $encryptValue = $encryptValue . $codeBigOne[$j];
                            break;
                        }
                    }
                }
            }
        }
    }

    return $encryptValue;
    // 
    // echo $dejump;
}

function decrypt($argv1) {
    $baseValue = $argv1;
    $encode = 0;
    $tmpencode = "";
    $injection = 0;

    $codeOne = array("w4Hy", "Fg4c", "as60", "D8cr", "WnT6", "kls0", "SnTT", "c008", "LSqP", "T6Ra");
    $codeBigOne = array("x", "j", "V", "p", "K", "d", "f", "Y", "5", "t");
    $codeSave = array("M", "c", "B", "n", "N", "m", "Q", "u", "X", "q");

    for ($i = 0; $i < 10; $i++) {
        if ($baseValue == $codeOne[$i]) {
            $encode = $i;
            break;
        }
    }
    if ($encode == 0) {
        $baseToChr = str_split($baseValue, 1);
        $end = count($baseToChr);
        for ($i = 0; $i < $end; $i++) {
            $confirm = 0;
            for ($j = 0; $j < 10; $j++) {
                if ($baseToChr[$i] == $codeBigOne[$j]) {
                    $tmpencode = $tmpencode . $j;
                    $confirm = 1;
                    break;
                }
            }
            if ($confirm == 0) {
                $validation = 0;
                for ($j = 0; $j < 10; $j++) {
                    if ($baseToChr[$i] == $codeSave[$j]) {
                        $tmpp = str_split($tmpencode, 1);
                        $tmp = count($tmpp);
                        $tmpencode = $tmpencode . $tmpp[$tmp - 1];

                        $validation = 1;
                        break;
                    }
                }
                if ($validation = 0) {
                    $tmpencode = "0";
                    $injection = 1;
                    $i = $end;
                }
            }
        }
        if ($injection == 0) {
            $tmp = intval($tmpencode);
            $encode = (($tmp * 2) - 8) / 1996;
            // $encode= $tmp;
        } else {
            $encode = 0;
        }
    }
    return $encode;
}
