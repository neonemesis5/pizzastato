<?php
class CurrencyConverter {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function getConversionRate($fromCurrencyId, $toCurrencyId) {
        try {
            $query = "SELECT monto FROM tasa WHERE moneda_id1 = :fromCurrencyId AND moneda_id2 = :toCurrencyId AND status = 'A' LIMIT 1";
            $stmt = $this->dbConnection->prepare($query);
            $stmt->bindParam(':fromCurrencyId', $fromCurrencyId, PDO::PARAM_INT);
            $stmt->bindParam(':toCurrencyId', $toCurrencyId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['monto'];
            } else {
                throw new Exception("No se encontró una tasa activa para la conversión.");
            }
        } catch (Exception $e) {
            die("Error al obtener la tasa de conversión: " . $e->getMessage());
        }
    }

    public function convert($amount, $fromCurrencyId, $toCurrencyId) {
        $rate = $this->getConversionRate($fromCurrencyId, $toCurrencyId);
        return $amount * $rate;
    }
}
