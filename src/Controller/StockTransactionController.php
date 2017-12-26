<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\StockTransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method User getUser
 */
class StockTransactionController extends AbstractController
{
    /**
     * @var StockTransactionService
     */
    private $stockTransactionService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * StockTransactionController constructor.
     *
     * @param StockTransactionService $stockTransactionService
     * @param RequestStack $requestStack
     */
    public function __construct(StockTransactionService $stockTransactionService, RequestStack $requestStack)
    {
        $this->stockTransactionService = $stockTransactionService;
        $this->requestStack  = $requestStack;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function createAction(Request $request): JsonResponse
    {
        try {
            $stockTransaction = $this->stockTransactionService->create(
                $this->getUser()->getId(),
                json_decode($request->getContent())
            );
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(
            $stockTransaction,
            Response::HTTP_CREATED,
            ['Location' => "{$request->getRequestUri()}/{$stockTransaction->getId()}"]
        );
    }

    /**
     * @return JsonResponse
     */
    public function listAction(): JsonResponse
    {
        $stockTransactions = $this->stockTransactionService->read($this->getUser()->getId());

        return new JsonResponse($stockTransactions);
    }

    /**
     * @param string $id
     *
     * @return Response
     */
    public function getAction(string $id): Response
    {
        try {
            $stockTransactions = $this->stockTransactionService->read($this->getUser()->getId(), $id);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        if (!$stockTransactions) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(reset($stockTransactions));
    }

    /**
     * @param string $id
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function putAction(string $id, Request $request): Response
    {
        try {
            $this->stockTransactionService->update(
                $this->getUser()->getId(),
                $id,
                json_decode($request->getContent())
            );
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $id
     *
     * @return JsonResponse
     */
    public function deleteAction(string $id): Response
    {
        try {
            $this->stockTransactionService->delete($this->getUser()->getId(), $id);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @return JsonResponse
     */
    public function getPortfolioAction(): JsonResponse
    {
        $portfolioSummary = $this->stockTransactionService->getPortfolioSummary(
            $this->getUser()->getId()
        );
        $stockSymbols = array_column($portfolioSummary, 'symbol');

        return new JsonResponse([
            'portfolio' => $portfolioSummary,
            'currentPrices' => $this->stockTransactionService->getStockPriceQuotes(...$stockSymbols),
        ]);
    }
}
