<?php

namespace MathildeGrise\Recrutement\KataRefacto;

use Exception;
use MathildeGrise\Recrutement\KataRefacto\Framework\Application_ServiceLocator;
use MathildeGrise\Recrutement\KataRefacto\Framework\ApplicationContext;
use MathildeGrise\Recrutement\KataRefacto\Framework\Response;
use MathildeGrise\Recrutement\KataRefacto\Models\Customer;
use MathildeGrise\Recrutement\KataRefacto\Models\EReservation;
use MathildeGrise\Recrutement\KataRefacto\Models\Product;
use MathildeGrise\Recrutement\KataRefacto\Models\Store;

class CreateReservation
{
    /**
     * log level used for logging E-reservation steps
     */
    const INFO_LOG_LEVEL = 'INFO';

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
     * @var Product
     */
    protected $product;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var Customer
     */
    protected $customer;

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
    public function create(array $data)
    {
        $response = new Response();

        // add logs to log init process
        Application_ServiceLocator::get('logger')->log('init create E-reservation process with parameters: ' . json_encode($data), self::INFO_LOG_LEVEL);

        /* check and set all request params */
        Application_ServiceLocator::get('logger')->log('check parameters', self::INFO_LOG_LEVEL);
        // check if all parameters are passed
        if (!$this->checkData($data)) {
            return $response->setCode(403);
        }
        $this->store = ApplicationContext::getInstance()->getCurrentStore();
        // set product from SKU
        $this->product = Application_ServiceLocator::get('product.repository')->getProductFromSkuByStore($data[self::PRODUCT_SKU_PARAM], $this->store->getId());
        // set costumer from costumer id
        $this->customer = Application_ServiceLocator::get('customer.repository')->getById($data[self::CUSTOMER_ID_PARAM]);

        /* create e-reservation */

        /* check there is stock for the product */
        Application_ServiceLocator::get('logger')->log('Determine if there is stock for the product on the store', self::INFO_LOG_LEVEL);
        $stockService = Application_ServiceLocator::get('stock.product_availability');
        try {
            $stock = $stockService->getStockLevelByStore($this->store->getId(), $this->product);
        } catch (Exception $e) {
            // log the error status
            Application_ServiceLocator::get('logger')->log("Error StockByStore - " . $e->getMessage(), self::INFO_LOG_LEVEL);
            return $response->setCode(500);
        }
        // Check availability
        if (!$stock['Available']) {
            return $response->setCode(500);
        }

        // Persist new e-reservation in DB
        Application_ServiceLocator::get('logger')->log('Create new E-reservation', self::INFO_LOG_LEVEL);
        $ereservationRepository = Application_ServiceLocator::get('ereservation.repository');
        $price = $this->product->getPrice();
        if ($price > 100000) {
            if (preg_match('/^WAT/', $this->product->getSku())) { // A watch
                $price = $price * (1 + 0.15);
            } else {
                $price = $price * (1 + 0.10);
            }
        }
        $id = $ereservationRepository->nextId();
        // create new E-reservation
        $this->eReservation = new EReservation(
            $id,
            $this->store->getId(),
            $this->product->getSKU(),
            $price,
            $this->customer->getId()
        );
        $ereservationRepository->save($this->eReservation);

        // Send email
        Application_ServiceLocator::get('logger')->log('Notify Sales team of new ereservation', self::INFO_LOG_LEVEL);
        $mailer = Application_ServiceLocator::get('mailer');
        try {
            $mailer->sendNewEReservation(ApplicationContext::getInstance()->getConfig()['sales_team_email'], $this->eReservation->getId());
        } catch (Exception $e) {
            Application_ServiceLocator::get('logger')->log("Error Send new Ereservation email - " . $e->getMessage(), self::INFO_LOG_LEVEL);
            throw $e;
        }

        Application_ServiceLocator::get('logger')->log('E-reservation created with success' . json_encode($data), self::INFO_LOG_LEVEL);

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
}