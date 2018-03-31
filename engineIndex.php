<?php
    ini_set('memory_limit', '-1');
    $word = array();

    function indexFile($input, $index) {
        global $word;
        preg_match_all("/\S+/i", $input, $arr);
        $length = count($arr);
        foreach ($arr[0] as $key => $value) {
            $value = strtolower($value);
                if (!isset($word[$value]))
                    $word[$value] = array();
                if (!isset($word[$value][$index]))
                    $word[$value][$index] = 1;
        }
    }

    function readLine($filein, $fileout, $index) {
        $str = fgets($filein);

        $str = trim($str);
        $arr = explode("\t",$str);
        if (!isset($arr[0])) return false;
        $arr[0] = trim($arr[0]);
        if (!isset($arr[1])) return false;
        $arr[1] = trim($arr[1]);
        if (!isset($arr[2])) return false;
        $arr[2] = trim($arr[2]);

        //fprintf($fileout, "%s", $str."\n");

        $regex = "[^1-9a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý]+";
        $arr[1] = preg_replace("/$regex/siU"," ", $arr[1]);
        $arr[1] = preg_replace("/\s+/", " ",$arr[1]);
        $arr[1] = trim($arr[1]);
        indexFile($arr[1], $index);

        $arr[2] = preg_replace("/\[.*\]/siU", " ",$arr[2]);
        $arr[2] = preg_replace("/\{.*\}/siU", " ",$arr[2]);
        $arr[2] = preg_replace("/$regex/siU", " ", $arr[2]);
        $arr[2] = preg_replace("/\s+/", " ",$arr[2]);
        $arr[2] = trim($arr[2]);
        indexFile($arr[2], $index);

        return true;
    }

    function systemFile($file) {
        global $word;
        $handle = fopen($file,"a");
        foreach ($word as $key => $value) {
            $data = json_encode($word[$key]);
            fwrite($handle, $key."\t".$data."\n");
        }
        fclose($handle);
    }

    $time = time();
    $usage = memory_get_usage();
    $baseDirectory = "data/";
    $systemDirectory = "system/";

    $handle = fopen("data.txt", "r");
    $temp = fopen("count.txt", "w");
    $index = 1;
    $cluter = 0;
    while (!feof($handle)) {
        // if (($index - 1) % 100 == 0) {
        //     if (isset($fileout))
        //         fclose($fileout);
        //     $cluter++;
        //     $filename = $baseDirectory.$cluter.".txt";
        //     $fileout = fopen($filename, "w");
        // }
        $fileout = 0;
        if (readline($handle, $fileout, $index)){
            fwrite($temp, $index . "\r\n");
            $index++;
        }

    }
    //if (isset($fileout)) fclose($fileout);
    fclose($temp);
    fclose($handle);

    $filename = $systemDirectory."system.txt";
    systemFile($filename);
    //echo memory_get_usage() - $usage;
    echo time() - $time;
    echo "DONE!"
?>