<?php

namespace App\Services;

use App\Entity\StockTransaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StockTransactionService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ApiClientFactory $apiClientFactory
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->apiClient = $apiClientFactory->createApiClient();
    }

    /**
     * @param string $userId
     * @param \stdClass $requestPayload
     *
     * @return StockTransaction
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(string $userId, \stdClass $requestPayload): StockTransaction
    {
        $stockTransaction = new StockTransaction();
        $this->buildStockTransaction($stockTransaction, $requestPayload, $userId);

        $this->entityManager->persist($stockTransaction);
        $this->entityManager->flush();

        return $stockTransaction;
    }

    /**
     * @param string $userId
     * @param string $orderId
     * @param \stdClass $requestPayload
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(string $userId, string $orderId, \stdClass $requestPayload): void
    {
        $stockTransaction = $this->findTransaction($userId, $orderId);

        $this->buildStockTransaction($stockTransaction, $requestPayload, $userId);

        $this->entityManager->persist($stockTransaction);
        $this->entityManager->flush();
    }

    /**
     * @param string $userId
     * @param string|null $orderId
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function read(string $userId, string $orderId = null): array
    {
        $criteria = [
            'user' => $userId,
        ];

        if ($orderId) {
            $this->validateId($orderId);
            $criteria['id'] = $orderId;
        }

        $stockTransactions = $this
            ->entityManager
            ->getRepository(StockTransaction::class)
            ->findBy($criteria);

        return $stockTransactions;
    }

    /**
     * @param string $userId
     * @param string $orderId
     *
     * @throws \InvalidArgumentException
     */
    public function delete(string $userId, string $orderId): void
    {
        $stockTransaction = $this->findTransaction($userId, $orderId);

        $this->entityManager->remove($stockTransaction);
        $this->entityManager->flush();
    }

    /**
     * @param string[] ...$stockSymbols
     *
     * @return array
     */
    public function getStockPriceQuotes(string ...$stockSymbols): array
    {
        try {
            $results = $this->apiClient->getQuotes($stockSymbols);
        } catch (GuzzleException $e) {
            return [];
        }

        $stockQuotes = [];
        /** @var Quote $result */
        foreach ($results as $result) {
            $stockQuotes[$result->getSymbol()] = [
                'price' => $result->getRegularMarketPreviousClose(),
                'description' => $result->getShortName(),
            ];
        }
        return $stockQuotes;
    }

    /**
     * @param string $userId
     *
     * @return array
     */
    public function getPortfolioSummary(string $userId): array
    {
        return $this
            ->entityManager
            ->getRepository(StockTransaction::class)
            ->getPortfolioSummary($userId);
    }

    /**
     * @param StockTransaction $stockTransaction
     * @param \stdClass $requestPayload
     * @param string $userId
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \InvalidArgumentException
     */
    private function buildStockTransaction(StockTransaction $stockTransaction, \stdClass $requestPayload, string $userId): void
    {
        $stockSymbol = $requestPayload->symbol;
        $stockPrice = $this->getStockPrice($stockSymbol, new \DateTime($requestPayload->date));
        if (is_null($stockPrice)) {
            throw new \InvalidArgumentException('Invalid date provided');
        }
        /** @var User $user */
        $user = $this->entityManager->getReference(User::class, $userId);
        $stockTransaction
            ->setType($requestPayload->type)
            ->setAmount($requestPayload->amount)
            ->setPrice($stockPrice)
            ->setSymbol($stockSymbol)
            ->setUser($user);

        $violationList = $this->validator->validate($stockTransaction);

        if ($violationList->count()) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param string $userId
     * @param string $stockTransactionId
     *
     * @return StockTransaction
     * @throws \InvalidArgumentException
     */
    private function findTransaction(string $userId, string $stockTransactionId): StockTransaction
    {
        $stockTransaction = $this
            ->entityManager
            ->getRepository(StockTransaction::class)
            ->findOneBy([
                'user' => $userId,
                'id' => $stockTransactionId,
            ]);

        if (!$stockTransaction) {
            throw new \InvalidArgumentException(
                'Transaction with that id does not exist or you do not have access to it.'
            );
        }

        return $stockTransaction;
    }

    /**
     * @param string $stockSymbol
     * @param \DateTime $startDate
     *
     * @return float|null
     */
    private function getStockPrice(string $stockSymbol, \DateTime $startDate): ?float
    {
        $endDate = clone $startDate;
        $endDate->modify('+1 day');
        try {
            $results = $this->apiClient->getHistoricalData(
                $stockSymbol,
                ApiClient::INTERVAL_1_DAY,
                $startDate,
                $endDate
            );
        } catch (ApiException|GuzzleException $e) {
            return null;
        }

        if (!$results) {
            return null;
        }

        /** @var HistoricalData $historicalData */
        $historicalData = reset($results);
        return $historicalData->getAdjClose();
    }

    /**
     * @param string $orderId
     *
     * @throws \InvalidArgumentException
     */
    private function validateId(string $orderId): void
    {
        $uuidConstraint = new Uuid();
        $uuidConstraint->versions = [Uuid::V4_RANDOM];
        $violationList = $this->validator->validate($orderId, $uuidConstraint);

        if ($violationList->count()) {
            throw new \InvalidArgumentException('Transaction ID is not a valid ID');
        }
    }
}
