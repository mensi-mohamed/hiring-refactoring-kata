<?php

namespace MathildeGrise\Recrutement\KataRefacto\Tests;

use MathildeGrise\Recrutement\KataRefacto\CreateReservation;
use MathildeGrise\Recrutement\KataRefacto\Framework\Application_ServiceLocator;
use MathildeGrise\Recrutement\KataRefacto\Framework\ApplicationContext;
use MathildeGrise\Recrutement\KataRefacto\Framework\Logger;
use MathildeGrise\Recrutement\KataRefacto\Framework\Response;
use MathildeGrise\Recrutement\KataRefacto\HttpMailer;
use MathildeGrise\Recrutement\KataRefacto\HttpStockService;
use MathildeGrise\Recrutement\KataRefacto\Models\Customer;
use MathildeGrise\Recrutement\KataRefacto\Models\EReservation;
use MathildeGrise\Recrutement\KataRefacto\Models\Product;
use MathildeGrise\Recrutement\KataRefacto\Models\Store;
use MathildeGrise\Recrutement\KataRefacto\Repositories\CustomerRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\EReservationRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\ProductRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class CreateReservationTest extends TestCase
{
    use ProphecyTrait;

    const SAlES_TEAM_MAIL = 'sales@mathilde-grise.com';

    /** @var CreateReservation */
    private $SUT;

    /** @var EReservationRepository  */
    private $EReservationRepository;

    /** @var HttpStockService */
    private $stock;

    /** @var Store  */
    private $store;

    /** @var Product  */
    private $product;

    /** @var httpMailer */
    private $mailer;

    public function __construct()
    {
        parent::__construct();
        $this->SUT = new CreateReservation();
        $storeId = 17;
        ApplicationContext::$currentStore = $this->store = new Store($storeId);
        ApplicationContext::$config = ['sales_team_email' => self::SAlES_TEAM_MAIL];
        $sku = 'test';
        $price = 200000;
        $this->product = new Product($sku, $price);
        $customer = new Customer(1);
        $this->stock = $this->prophesize(HttpStockService::class);
        $this->mailer = $this->prophesize(HttpMailer::class);
        $this->EReservationRepository = $this->prophesize(EReservationRepository::class);
        Application_ServiceLocator::$services = [
            'logger' => new Logger(),
            'customer.repository' => new CustomerRepository([$customer->getId() => $customer]),
            'product.repository' => new ProductRepository([
                $this->store->getId() => [
                    $sku => $this->product
                ]
            ]),
            'stock.product_availability' => $this->stock->reveal(),
            'ereservation.repository' => $this->EReservationRepository->reveal(),
            'mailer' => $this->mailer->reveal()
        ];

    }

    /**
     * @test
     */
    public function it_creates_an_ereservation()
    {
        //Given
        $reservationId = 1;
        $expectedResponse = (new Response())
            ->setCode(201)
            ->setData(
                [
                    'Available' => true,
                    'id' => $reservationId
                ]);

        $this->EReservationRepository->nextId()->willReturn($reservationId);
        $this->EReservationRepository->save(Argument::any())->shouldBeCalled();

        $this->stock->getStockLevelByStore($this->store->getId(), $this->product)->willReturn($expectedResponse);
        $this->mailer->sendNewEReservation(self::SAlES_TEAM_MAIL, $reservationId)->willReturn(true);

        //When
        $response = $this->SUT->create([
            'productsku' => 'test',
            'customerid' => 1
        ]);

       //Then
        $this->assertSame(201, $response->getCode());
        $this->assertSame($reservationId, $response->getData()['id']);
    }
}
