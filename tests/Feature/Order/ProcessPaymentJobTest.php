<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Modules\Gateway\Services\PaymentResult;
use Modules\Gateway\Services\PaymentService;
use Modules\Gateway\Models\Gateway;
use Modules\Order\Jobs\ProcessPaymentJob;
use Modules\Order\Models\Transaction;
use Modules\Client\Models\Client;
use Tests\TestCase;

class ProcessPaymentJobTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingTransaction(array $overrides = []): Transaction
    {
        $client = Client::query()->create([
            'name'  => 'Test Client',
            'email' => 'client@example.com',
        ]);

        return Transaction::query()->create(array_merge([
            'client_id'         => $client->id,
            'gateway_id'        => null,
            'external_id'       => null,
            'status'            => 'pending',
            'amount'            => 1000,
            'card_last_numbers' => '1234',
        ], $overrides));
    }

    public function test_job_sets_transaction_to_paid_on_success(): void
    {
        $gateway = Gateway::query()->create([
            'name'      => 'gateway1',
            'is_active' => true,
            'priority'  => 1,
        ]);

        $transaction = $this->createPendingTransaction();

        $paymentService = Mockery::mock(PaymentService::class);
        $paymentService->shouldReceive('process')
            ->once()
            ->with(Mockery::type(Transaction::class), [], [])
            ->andReturn(PaymentResult::success($gateway->id, 'ext-123'));

        $job = new ProcessPaymentJob($transaction->id, [], []);
        $job->handle($paymentService);

        $transaction->refresh();
        $this->assertSame('paid', $transaction->status);
        $this->assertSame($gateway->id, $transaction->gateway_id);
        $this->assertSame('ext-123', $transaction->external_id);
    }

    public function test_job_sets_transaction_to_failed_on_failure(): void
    {
        $transaction = $this->createPendingTransaction();

        $paymentService = Mockery::mock(PaymentService::class);
        $paymentService->shouldReceive('process')
            ->once()
            ->andReturn(PaymentResult::failure());

        $job = new ProcessPaymentJob($transaction->id, [], []);
        $job->handle($paymentService);

        $transaction->refresh();
        $this->assertSame('failed', $transaction->status);
        $this->assertNull($transaction->gateway_id);
        $this->assertNull($transaction->external_id);
    }
}
