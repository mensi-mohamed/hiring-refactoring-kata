<?php

namespace MathildeGrise\Recrutement\KataRefacto;

use Exception;
use MathildeGrise\Recrutement\KataRefacto\Framework\Application_ServiceLocator;
use MathildeGrise\Recrutement\KataRefacto\Framework\ApplicationContext;
use MathildeGrise\Recrutement\KataRefacto\Framework\Logger;
use MathildeGrise\Recrutement\KataRefacto\Framework\Response;
use MathildeGrise\Recrutement\KataRefacto\Models\Customer;
use MathildeGrise\Recrutement\KataRefacto\Models\EReservation;
use MathildeGrise\Recrutement\KataRefacto\Models\Product;
use MathildeGrise\Recrutement\KataRefacto\Models\Store;
use MathildeGrise\Recrutement\KataRefacto\Repositories\CustomerRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\EReservationRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\ProductRepository;
use MathildeGrise\Recrutement\KataRefacto\Repositories\StockRepository;

class CreateReservation
{
    /**
     * used data parameters
     */
    const PRODUCT_SKU_PARAM = "productsku";
    const CUSTOMER_ID_PARAM = "customerid";

    /**
     * list of all mandatory data
     */
    const MANDATORY_PARAMS = [
        self::PRODUCT_SKU_PARAM,
        self::CUSTOMER_ID_PARAM,
    ];

    /**
     * @var EReservation
     */
    protected $eReservation;


    /**
     * create e-reservation
     *
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function create(Logger $logger, ProductRepository $productRepository, CustomerRepository $customerRepository,
                           HttpStockService $stockService, EReservationRepository $EReservationRepository,
                           HttpMailer $mailer, array $data)
    {
        $response = new Response();

        // add logs to log init process
        $logger->log('init create E-reservation process with parameters: ' . json_encode($data), Logger::INFO_LOG_LEVEL);

        /* check and set all request params */
        $logger->log('check parameters', Logger::INFO_LOG_LEVEL);

        // check if all parameters are passed
        if (!$this->checkData($data)) {
            return $response->setCode(403);
        }

        $store = ApplicationContext::getInstance()->getCurrentStore();

        // set product from SKU
        $oProd = $productRepository->getProductFromSkuByStore($data[self::PRODUCT_SKU_PARAM], $store->getId());

        // set user from costumer id
        $user = $customerRepository->getById($data[self::CUSTOMER_ID_PARAM]);
        /* check there is stock for the product */
        $logger->log('Determine if there is stock for the product on the store', Logger::INFO_LOG_LEVEL);

        try {
            $stock = $stockService->getStockLevelByStore($store->getId(), $oProd);
        } catch (Exception $e) {
            // log the error status
            $logger->log("Error StockByStore - " . $e->getMessage(), Logger::INFO_LOG_LEVEL);
            return $response->setCode(500);
        }
        // Check availability
        if (!$stock->getData()['Available']) {
            return $response->setCode(500);
        }

        // Persist new e-reservation in DB
        $logger->log('Create new E-reservation', Logger::INFO_LOG_LEVEL);
        $price = $oProd->getPrice();
        $finalPrice = $this->processPrice($price, $oProd);

        // create new E-reservation
        $this->eReservation =  EReservation::create(
            $EReservationRepository->nextId(),
            $store->getId(),
            $oProd->getSKU(),
            $finalPrice,
            $user->getId()
        );
        $EReservationRepository->save($this->eReservation);

        $this->sendEmail($logger, $mailer);

        $logger->log('E-reservation created with success' . json_encode($data), Logger::INFO_LOG_LEVEL);

        // format final success response
        $response->setCode(201);
        $response->setData(['id' => $this->eReservation->getId()]);

        return $response;
    }

    /**
     * check if all parameters are passed
     *
     * @param array $data
     * @return bool
     */
    private function checkData(array $data)
    {
        foreach (self::MANDATORY_PARAMS as $param) {
            if (!isset($data[$param])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Logger $logger
     * @param HttpMailer $mailer
     * @throws Exception
     */
    private function sendEmail(Logger $logger, HttpMailer $mailer)
    {
        // Send email
        $logger->log('Notify Sales team of new ereservation', Logger::INFO_LOG_LEVEL);

        try {
            $mailer->sendNewEReservation(ApplicationContext::getInstance()->getConfig()['sales_team_email'], $this->eReservation->getId());
        } catch (Exception $e) {
            $logger->log("Error Send new Ereservation email - " . $e->getMessage(), Logger::INFO_LOG_LEVEL);
            throw $e;
        }
    }

    /**
     * @param int $price
     * @param Product $oProd
     * @return int
     */
    private function processPrice(int $price, Product $oProd): int
    {
        if ($price > 100000) {
            if (preg_match('/^WAT/', $oProd->getSku())) { // A watch
                $price = $price * (1 + 0.15);
            } else {
                $price = $price * (1 + 0.10);
            }
        }

        return $price;
    }
}
