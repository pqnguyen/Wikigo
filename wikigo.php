<?php
    $word = array();
    $result = array();
    function init() {
        global $word;
        //$word = json_decode(file_get_contents("system/system.json"), true);
        //$word = unserialize(file_get_contents("system/system.json"));
        $handle = fopen("system/system.txt", "r");
        while (!feof($handle)) {
            $str = fgets($handle);
            if (!strlen($str)) continue;
            $arr = explode("\t", $str);
            $arr[0] = trim($arr[0]);
            $arr[1] = trim($arr[1]);
            $word[$arr[0]] = json_decode($arr[1], true);
        }
        fclose($handle);
    }

    function formatQuery($str) {
        $regex = "[^1-9a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý]+";
        $str = preg_replace("/$regex/siU", " ", $str);
        $str = trim($str);
        preg_match_all("/\S+/i", $str, $arr);
        return $arr[0];
    }

    function search($str) {
        global $word;
        $res = array();
        $wordsInPage = array();
        $query = formatQuery($str);
        $length = 0;
        foreach ($query as $key => $value) {
            $value = strtolower($value);
            if (isset($word[$value])) {
                $length++;
                foreach ($word[$value] as $key => $value) {
                    if (!isset($wordsInPage[$key])) {
                        $wordsInPage[$key] = 1;
                    }
                    else
                        $wordsInPage[$key]++;
                    $pos = $wordsInPage[$key];
                    if (isset($res[$pos - 1][$key])) {
                        unset($res[$pos - 1][$key]);
                    }
                    if (!isset($res[$pos][$key])) {
                        $res[$pos][$key] = 1;
                    }
                }
            }
        }

        global $result;
        //print_r($res);
        $count = 0;
        while (($length > 0) && ($count != 10)) {
            while (((!isset($res[$length])) || (count($res[$length]) == 0)))
                $length--;
            $page = key($res[$length]);
            //echo $page . "<br>";
            array_push($result, $page);
            unset($res[$length][$page]);
            $count++;
        }
    }

    $searchResult = array();
    function formatStr($str) {
        global $searchResult;
        $str = trim($str);
        $arr = explode("\t",$str);
        $arr[0] = trim($arr[0]);
        $arr[1] = trim($arr[1]);
        $arr[2] = trim($arr[2]);

        $res['link'] = $arr[0];
        $res['title'] = $arr[1];
        $res['desc'] = $arr[2];
        array_push($searchResult, $res);
    }

    function getPage($index) {
        $cluster = (int)($index / 100);
        $row = $index % 100;
        if ($row != 0) {            
            $cluster++;
            $handle = fopen("data/" . $cluster . ".txt", "r");
                while ($row != 0) {
                    $str = fgets($handle);
                    $row--;
                }
            fclose($handle);
            formatStr($str);
        }
    }

    function main() {
        global $word;
        global $result;
        global $searchResult;
        if (isset($_POST["query"])) {
            init();
            $str = $_POST["query"];
            search($str);
            // //print_r($word);
            //unset($word);
            foreach ($result as $key => $value) {
                getPage($value);
            }
            // //print_r($searchResult);
            return json_encode($searchResult);
        }
        else
        return "{}";
    }

    echo main();
?>