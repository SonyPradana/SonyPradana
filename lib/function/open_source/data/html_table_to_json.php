<?php
/**
 * This is just a step by step demonstration to convert HTML table into PHP array, 
 * we can make a better algorithm to directly map the key and value of array 
 * instead of separate it into three sections 
 * (get header name, detail, and then map the header name and detail data). 
 * If we want, with a little enhancement, we can use this PHP array ready for a database (e.g. insert data).
 * 
 * The function getElementsByTagName() is very useful here.
 *  With this DOM function, we can also play with all HTML content of webpages, 
 * not only with HTML table. Maybe we want to search link inside a web page, 
 * follow the link to search another link, or maybe it will be a web crawler, 
 * one of the essential parts of a search engine, like Google's, who knows.
 * 
 * @author Garbel Nervadof
 * @link https://www.codeproject.com/Tips/1074174/Simple-Way-to-Convert-HTML-Table-Data-into-PHP-Arr#_articleTop
 */
?>

<?php
    /**
     * convert html table to array 
     * @param string $html_url 
     * html dokumen yg terdapat table 
     * @return array 
     * array dalam bentuk json
     */
    function htmlToJson($html_url){
            
        $htmlContent = file_get_contents($html_url);
            
        $DOM = new DOMDocument();
        $DOM->loadHTML($htmlContent);
        
        $Header = $DOM->getElementsByTagName('th');
        $Detail = $DOM->getElementsByTagName('td');

        //#Get header name of the table
        foreach($Header as $NodeHeader) 
        {
            $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
        }
        //print_r($aDataTableHeaderHTML); die();

        //#Get row data/detail table without header name as key
        $i = 0;
        $j = 0;
        foreach($Detail as $sNodeDetail) 
        {
            $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
            $i = $i + 1;
            $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
        }
        //print_r($aDataTableDetailHTML); die();
        
        //#Get row data/detail table with header name as key and outer array index as row number
        for($i = 0; $i < count($aDataTableDetailHTML); $i++)
        {
            for($j = 0; $j < count($aDataTableHeaderHTML); $j++)
            {
                $aTempData[$i][$aDataTableHeaderHTML[$j]] = $aDataTableDetailHTML[$i][$j];
            }
        }
        $aDataTableDetailHTML = $aTempData; 
        unset($aTempData);
        // print_r($aDataTableDetailHTML); 
        

        return json_encode($aDataTableDetailHTML);
    }
