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
    private $service;

    /** @var EReservationRepository  */
    private $EReservationRepository;

    /** @var HttpStockService */
    private $stock;

    /** @var Store  */
    private $store;

    /** @var Product  */
    private $product;

    /** @var ProductRepository  */
    private $productRepository;

    /** @var CustomerRepository  */
    private $customerRepository;

    /** @var httpMailer */
    private $mailer;

    private $logger;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CreateReservation();
        $storeId = 17;
        ApplicationContext::$currentStore = $this->store = new Store($storeId);
        ApplicationContext::$config = ['sales_team_email' => self::SAlES_TEAM_MAIL];
        $this->stock = $this->prophesize(HttpStockService::class);
        $this->mailer = $this->prophesize(HttpMailer::class);
        $this->logger = $this->prophesize(Logger::class);
        $this->customerRepository = $this->prophesize(CustomerRepository::class);
        $this->productRepository = $this->prophesize(ProductRepository::class);
        $this->EReservationRepository = $this->prophesize(EReservationRepository::class);

    }

    /**
     * @test
     */
    public function createReservationSucess()
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
        $expectedUser = new Customer(1);
        $sku = 'test';
        $price = 200000;
        $ExpectedProduct = new Product($sku, $price);
        $params = [
            'productsku' => 'test',
            'customerid' => 1
        ];

        $this->EReservationRepository->nextId()->willReturn($reservationId);
        $this->EReservationRepository->save(Argument::any())->shouldBeCalled();
        $this->productRepository->reveal();
        $this->customerRepository->reveal();
        $this->productRepository->getProductFromSkuByStore($params[CreateReservation::PRODUCT_SKU_PARAM], 17)->willReturn($ExpectedProduct);
        $this->customerRepository->getById($params[CreateReservation::CUSTOMER_ID_PARAM])->willReturn($expectedUser);

        $this->stock->getStockLevelByStore($this->store->getId(), $ExpectedProduct)->willReturn($expectedResponse);
        $this->mailer->sendNewEReservation(self::SAlES_TEAM_MAIL, $reservationId)->willReturn(true);

        //When
        $response = $this->service->create($this->logger->reveal(), $this->productRepository->reveal(),
            $this->customerRepository->reveal(), $this->stock->reveal(), $this->EReservationRepository->reveal(),
            $this->mailer->reveal(), $params);

       //Then
        $this->assertSame(201, $response->getCode());
        $this->assertSame($reservationId, $response->getData()['id']);
    }

    /**
     * @test
     */
    public function CreateReservationWhenNoDataSent()
    {
        //Given
        $reservationId = 1;
        $params = [
        ];

        $this->EReservationRepository->nextId()->shouldNotBeCalled($reservationId);
        $this->mailer->sendNewEReservation(self::SAlES_TEAM_MAIL, $reservationId)->shouldNotBeCalled();

        //When
        $response = $this->service->create($this->logger->reveal(), $this->productRepository->reveal(),
            $this->customerRepository->reveal(), $this->stock->reveal(), $this->EReservationRepository->reveal(),
            $this->mailer->reveal(), $params);

       //Then
        $this->assertSame(403, $response->getCode());
    }
}
