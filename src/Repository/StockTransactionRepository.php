<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class StockTransactionRepository extends EntityRepository
{
    /**
     * @param string $userId
     *
     * @return array
     */
    public function getPortfolioSummary(string $userId): array
    {
        $sql = <<<SQL
SELECT
  symbol,
  total_buy_amount,
  total_buy_price,
  total_sell_amount,
  total_sell_price,
  (total_buy_amount - total_sell_amount) total_amount,
  (total_sell_price - total_buy_price) total_profit
FROM (
  SELECT
    symbol,
    SUM(case when type = 'buy' then amount else 0 end) total_buy_amount,
    SUM(case when type = 'buy' then (amount * price) else 0 end) total_buy_price,
    SUM(case when type = 'sell' then amount else 0 end) total_sell_amount,
    SUM(case when type = 'sell' then (amount * price) else 0 end) total_sell_price
    FROM stock_transaction
    WHERE user_id = ?
  GROUP BY symbol
) stock_transaction_alias
SQL;

        $results = $this->getEntityManager()->getConnection()->fetchAll(
            $sql,
            [$userId]
        );

        return array_map(function (array $result) {
            foreach ($result as $key => &$value) {
                if (in_array($key, ['symbol'])) {
                    continue;
                }
                $value = (float) $value;
            }
            return $result;
        }, $results);
    }
}
