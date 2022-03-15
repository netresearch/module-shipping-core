<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;
use Netresearch\ShippingCore\Api\Data\Config\ValidationResultInterface;
use Netresearch\ShippingCore\Model\Config\Validator;

class Validate extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Sales::ship';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var ValidationResultInterface
     */
    private $result;

    /**
     * @var ResultFactory
     */
    private $resultPageFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Validator $validator,
        ValidationResultInterface $result,
        ResultFactory $resultPageFactory
    ) {
        $this->storeManager = $storeManager;
        $this->validator = $validator;
        $this->result = $result;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $store = (int) $this->getRequest()->getParam('store');
        $section = (string) $this->getRequest()->getParam('section');

        $this->result->set($this->validator->execute($store, $section));

        return $this->resultPageFactory->create(ResultFactory::TYPE_PAGE);
    }
}
