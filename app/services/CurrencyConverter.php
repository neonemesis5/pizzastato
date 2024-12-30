<?php

namespace App\Services;

class CurrencyConverter {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function getConversionRate($fromCurrencyId, $toCurrencyId) {
        $query = "SELECT monto FROM tasa WHERE moneda_id1 = :fromCurrencyId AND moneda_id2 = :toCurrencyId AND status = 'A'";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            ':fromCurrencyId' => $fromCurrencyId,
            ':toCurrencyId' => $toCurrencyId
        ]);
        return $stmt->fetchColumn();
    }

    public function convert($amount, $fromCurrencyId, $toCurrencyId) {
        $rate = $this->getConversionRate($fromCurrencyId, $toCurrencyId);
        return $amount * $rate;
    }
}
