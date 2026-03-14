<?php

declare(strict_types=1);

namespace Tests\Unit\Gateway;

use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Modules\Gateway\Repositories\GatewayRepository;
use Modules\Gateway\Services\Payment\AbstractGateway;
use Modules\Gateway\Services\Payment\GatewayFactory;
use Modules\Gateway\Services\PaymentResult;
use Modules\Gateway\Services\PaymentService;
use Modules\Order\Models\Transaction;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createTransaction(array $overrides = []): Transaction
    {
        $t = new Transaction();
        $t->id = 1;
        $t->amount = 1000;
        $t->client_id = 1;
        $t->gateway_id = null;
        $t->external_id = null;
        $t->status = 'pending';
        $t->card_last_numbers = '1234';
        foreach ($overrides as $key => $value) {
            $t->{$key} = $value;
        }
        return $t;
    }

    public function test_second_gateway_is_called_when_first_fails(): void
    {
        $gateway1 = (object) ['id' => 1, 'name' => 'gateway1', 'priority' => 1];
        $gateway2 = (object) ['id' => 2, 'name' => 'gateway2', 'priority' => 2];

        $gateways = new Collection([$gateway1, $gateway2]);

        $gatewayRepo = Mockery::mock(GatewayRepository::class);
        $gatewayRepo->shouldReceive('activeOrderedByPriority')
            ->once()
            ->andReturn($gateways);

        $strategy1 = Mockery::mock(AbstractGateway::class);
        $strategy1->shouldReceive('charge')
            ->once()
            ->andReturn(PaymentResult::failure());

        $strategy2 = Mockery::mock(AbstractGateway::class);
        $strategy2->shouldReceive('charge')
            ->once()
            ->andReturn(PaymentResult::success(null, 'ext-456'));

        $factory = Mockery::mock(GatewayFactory::class);
        $factory->shouldReceive('make')->with('gateway1')->once()->andReturn($strategy1);
        $factory->shouldReceive('make')->with('gateway2')->once()->andReturn($strategy2);

        $service = new PaymentService($gatewayRepo, $factory);
        $transaction = $this->createTransaction();

        $result = $service->process($transaction, [], []);

        $this->assertTrue($result->success);
        $this->assertSame(2, $result->gatewayId);
        $this->assertSame('ext-456', $result->externalId);
    }
}
