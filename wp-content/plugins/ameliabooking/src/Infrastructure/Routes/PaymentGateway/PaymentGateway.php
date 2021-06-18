<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See COPYING.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Routes\PaymentGateway;

use AmeliaBooking\Application\Controller\PaymentGateway\MolliePaymentController;
use AmeliaBooking\Application\Controller\PaymentGateway\MolliePaymentNotifyController;
use AmeliaBooking\Application\Controller\PaymentGateway\PayPalPaymentCallbackController;
use AmeliaBooking\Application\Controller\PaymentGateway\PayPalPaymentController;
use AmeliaBooking\Application\Controller\PaymentGateway\WooCommercePaymentController;
use Slim\App;

/**
 * Class PaymentGateway
 *
 * @package AmeliaBooking\Infrastructure\Routes\PaymentGateway
 */
class PaymentGateway
{
    /**
     * @param App $app
     */
    public static function routes(App $app)
    {
        $app->get('/payment/payPal/callback', PayPalPaymentCallbackController::class);

        $app->post('/payment/payPal', PayPalPaymentController::class);

        $app->post('/payment/wc', WooCommercePaymentController::class);

        $app->post('/payment/mollie/notify', MolliePaymentNotifyController::class);

        $app->post('/payment/mollie', MolliePaymentController::class);
    }
}
