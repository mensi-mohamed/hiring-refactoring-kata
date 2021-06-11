<?php

namespace MathildeGrise\Recrutement\KataRefacto\Tests;

use MathildeGrise\Recrutement\KataRefacto\CreateReservation;
use MathildeGrise\Recrutement\KataRefacto\Framework\Application_ServiceLocator;
use MathildeGrise\Recrutement\KataRefacto\Framework\ApplicationContext;
use MathildeGrise\Recrutement\KataRefacto\Framework\Logger;
use MathildeGrise\Recrutement\KataRefacto\HttpMailer;
use MathildeGrise\Recrutement\KataRefacto\HttpStockService;
use MathildeGrise\Recrutement\KataRefacto\Models\Customer;
use MathildeGrise\Recrutement\KataRefacto\Models\Product;
use MathildeGrise\Recrutement\KataRefacto\Models\Store;
use MathildeGrise\Recrutement\KataRefacto\Repositories\CustomerRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\EReservationRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\ProductRepository;
use PHPUnit\Framework\TestCase;

class CreateReservationTest extends TestCase
{
    /**
     * @var CreateReservation
     */
    private $SUT;
    private EReservationRepository $EReservationRepository;

    public function __construct()
    {
        parent::__construct();
        $this->SUT = new CreateReservation();
        $storeId = 17;
        ApplicationContext::$currentStore = $store = new Store($storeId);
        ApplicationContext::$config = ['sales_team_email' => 'sales@mathilde-grise.com'];
        $sku = 'test';
        $price = 200000;
        $product = new Product($sku, $price);
        $customer = new Customer(1);
        $this->EReservationRepository = new EReservationRepository([]);
        Application_ServiceLocator::$services = [
            'logger' => new Logger(),
            'customer.repository' => new CustomerRepository([$customer->getId() => $customer]),
            'product.repository' => new ProductRepository([
                $store->getId() => [
                    $sku => $product
                ]
            ]),
            'stock.product_availability' => new HttpStockService(),
            'ereservation.repository' => $this->EReservationRepository,
            'mailer' => new HttpMailer()
        ];
    }

    /**
     * @test
     */
    public function it_creates_an_ereservation()
    {
        $response = $this->SUT->create([
            'productsku' => 'test',
            'customerid' => 1
        ]);

        $this->assertSame(201, $response->getCode());
        $id = $response->getData()['id'];
        $ereservation = $this->EReservationRepository->getById($id);
        $this->assertSame($id, $ereservation->getId());
        $this->assertSame(17, $ereservation->getStoreId());
        $this->assertSame('test', $ereservation->getProductSku());
        $this->assertSame(1, $ereservation->getCustomerId());
    }
}
