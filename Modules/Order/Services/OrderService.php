<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Transaction;
use Modules\Client\Repositories\ClientRepository;
use Modules\Order\Repositories\TransactionProductRepository;
use Modules\Order\Repositories\TransactionRepository;
use Modules\Product\Repositories\ProductRepository;

class OrderService
{
    public function __construct(
        private readonly ClientRepository $clients,
        private readonly ProductRepository $products,
        private readonly TransactionRepository $transactions,
        private readonly TransactionProductRepository $transactionProducts,
    ) {}

    public function purchase(array $data): Transaction
    {
        return DB::transaction(function () use ($data): Transaction {
            $client = $this->resolveClient($data);
            $resolvedItems = $this->resolveItems($data);
            $totalAmount = $this->calculateTotalAmount($resolvedItems);
            $transaction = $this->createTransaction($data, $client->id, $totalAmount);

            $this->createTransactionProducts($transaction->id, $resolvedItems);

            return $transaction;
        });
    }

    private function resolveClient(array $data)
    {
        $clientData = $data['client'] ?? [];

        return $this->clients->findOrCreateByEmailOrName(
            $clientData['email'] ?? null,
            $clientData['name'] ?? null,
        );
    }

    private function resolveItems(array $data): array
    {
        $items = $data['items'] ?? [];

        if ($items === [] || ! is_array($items)) {
            throw new \RuntimeException('No items provided for purchase');
        }

        $resolvedItems = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                throw new \RuntimeException('Invalid item data');
            }

            $unitAmount = $this->resolveItemUnitAmount($productId);

            $resolvedItems[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'amount'     => $unitAmount,
            ];
        }

        return $resolvedItems;
    }

    private function resolveItemUnitAmount(int $productId): int
    {
        $product = $this->products->findById($productId);

        if ($product === null) {
            throw new \RuntimeException('Product not found');
        }

        return (int) $product->amount;
    }

    private function calculateTotalAmount(array $items): int
    {
        $total = 0;

        foreach ($items as $item) {
            $total += $item['amount'] * $item['quantity'];
        }

        return $total;
    }

    private function createTransaction(array $data, int $clientId, int $totalAmount): Transaction
    {
        $card = $data['card'] ?? [];
        $cardNumber = (string) ($card['number'] ?? '');
        $lastDigits = substr($cardNumber, -4);

        return $this->transactions->create([
            'client_id'         => $clientId,
            'gateway_id'        => null,
            'external_id'       => null,
            'status'            => 'pending',
            'amount'            => $totalAmount,
            'card_last_numbers' => $lastDigits,
        ]);
    }

    private function createTransactionProducts(int $transactionId, array $items): void
    {
        foreach ($items as $item) {
            $this->transactionProducts->create([
                'transaction_id' => $transactionId,
                'product_id'     => $item['product_id'],
                'quantity'       => $item['quantity'],
                'amount'         => $item['amount'],
            ]);
        }
    }
}

