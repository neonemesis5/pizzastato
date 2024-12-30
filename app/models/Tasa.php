<?php
namespace App\Models;

use PDO;

class Tasa extends BaseModel {
    public function getLatestRates() {
        $query = "
            SELECT moneda_id1, moneda_id2, monto 
            FROM tasa 
            WHERE status = 'A'
        ";
        $result = $this->prepareAndExecute($query)->fetchAll(PDO::FETCH_ASSOC);
        
        
        // Transformar el resultado en un formato útil
        $rates = [];
        foreach ($result as $row) {
            $rates[$row['moneda_id1'] . '_' . $row['moneda_id2']] = $row['monto'];
        }
        // echo '<pre>';
        //     print_r($rates['20_30']/$rates['20_10']);
        // echo '</pre>';
        $rates['10_30']=$rates['20_30']/$rates['20_10'];
        //    echo '<pre>';
        //     print_r($rates);
        // echo '</pre>';
        return $rates;
    }
}
