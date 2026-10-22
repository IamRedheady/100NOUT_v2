<?php


use WCSTORES\WC\MS\Controller\Sync\AssortmentController;
use WCSTORES\WC\MS\Wordpress\Actions\Actions;
use WCSTORES\WC\MS\Controller\Sync\WebHookSyncController;
use WCSTORES\WC\MS\Controller\Sync\ImagesController;
use WCSTORES\WC\MS\Controller\Kernel\FillingTablesWithDataController;
use WCSTORES\WC\MS\Controller\Sync\OrderController;
use WCSTORES\WC\MS\Controller\Sync\StockController;


Actions::queues('%webhook', [new WebHookSyncController(), 'sync'],10,3);
Actions::queues('%webhook_handler', [new WebHookSyncController(), 'handler'],10,4);

Actions::queues('%checking_for_image_updates', [new ImagesController(), 'sync']);
Actions::queues('%update_image', [new ImagesController(), 'update'], 10, 3);

Actions::queues('%filling_tables_with_data', [new FillingTablesWithDataController(), 'handler'], 10 ,2);

Actions::queues('%create_an_order_in_moysklad', [new OrderController(), 'createAnOrderInMoysklad']);
Actions::queues('%payment_complete_an_order_in_moysklad', [new OrderController(), 'paymentCompleteAnOrderInMoysklad']);
Actions::queues('%status_changed_an_order_in_moysklad', [new OrderController(), 'statusChangedAnOrderInMoysklad']);

Actions::queues('%starting_stock_synchronization_automatic', [new StockController(), 'startingStockSynchronizationAutomatic']);

Actions::queues('%sync_products', [new AssortmentController(), 'sync']);
Actions::queues('%sync_products_check', [new AssortmentController(), 'sync']);
Actions::queues('%starting_product_synchronization_automatic', [new AssortmentController(), 'startingProductSynchronizationAutomatic']);


Actions::queues('woocommerce_api_wcstores_start_run_queue', ['\WCSTORES\WC\MS\WooCommerce\Utilities\QueueUtil', 'actionSchedulerRun']);
