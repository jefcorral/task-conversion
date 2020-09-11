<?php

Class API {
    function toJson(){
        $datas = $this->convert();
        $json = json_encode($datas);
        return $json;
    }

    function toYaml(){
        $datas = $this->convert();
        return yaml_emit($datas);
    }

    function jsonToCsv(){
        $jsonDecoded = json_decode($this->toJson(), true);
 
        $csvFileName = 'task.csv';
        
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $fp = fopen('php://output', 'w');
        
        foreach($jsonDecoded as $row){
            fputcsv($fp, $row);
        }

        fclose($fp);
    }

    function convert(){
        if (($handle = fopen('customers.csv', "r")) !== FALSE) {
            $results = [];
            while(!feof($handle)) {
                $results[] = fgetcsv($handle);
            }
            $datas = [];
            $headers = [];
            foreach ($results[0] as $single_csv) {
                $headers[] = $single_csv;
            }
            
            foreach ($results as $key => $result) {
                if ($key === 0) {
                    continue;
                }
                if ($result) {
                    foreach ($headers as $header_key => $header_name) {
                        $datas[$key-1][$header_name] = $result[$header_key];
                    }
                }
            }
            fclose($handle);
        }
        return $datas;
    }
}
$convertion = new API();

if(array_key_exists('convertJson', $_POST) && isset($_POST['convertJson'])) { 
    $convertion = new API();
    header('Content-disposition: attachment; filename=file.json');
    header('Content-Type: application/json');
    echo $convertion->toJson();
} 
else if(array_key_exists('convertYaml', $_POST) && isset($_POST['convertYaml'])) { 
    $convertion = new API();
    header('Content-Type: application/x-yaml');
    echo $convertion->toYaml(); 
} 
else if(array_key_exists('convertJsonToCsv', $_POST) && isset($_POST['convertJsonToCsv'])) { 
    $convertion = new API();
    echo $convertion->jsonToCsv(); 
}
?>
