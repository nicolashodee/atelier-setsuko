<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Services\Placeholder;

use AmeliaBooking\Application\Services\Helper\HelperService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Bookable\Service\PackageCustomerService;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Factory\User\UserFactory;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\PackageCustomerServiceRepository;
use AmeliaBooking\Infrastructure\WP\Translations\FrontendStrings;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class PackagePlaceholderService
 *
 * @package AmeliaBooking\Application\Services\Notification
 */
class PackagePlaceholderService extends AppointmentPlaceholderService
{
    /**
     *
     * @return array
     *
     * @throws ContainerException
     */
    public function getEntityPlaceholdersDummyData()
    {
        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $dateFormat = $settingsService->getSetting('wordpress', 'dateFormat');

        return [
            'package_name'            => 'Package Name',
            'reservation_name'        => 'Package Name',
            'package_price'           => $helperService->getFormattedPrice(100),
            'package_description'     => 'Package Description',
            'package_duration'        => date_i18n($dateFormat, strtotime(date_create()->getTimestamp())),
            'reservation_description' => 'Package Description',
        ];
    }

    /**
     * @param array        $package
     * @param int          $bookingKey
     * @param string       $type
     * @param AbstractUser $customer
     *
     * @return array
     *
     * @throws ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws Exception
     */
    public function getPlaceholdersData($package, $bookingKey = null, $type = null, $customer = null)
    {
        return array_merge(
            $this->getPackageData($package),
            $this->getCompanyData(),
            $this->getCustomersData(
                $package,
                $type,
                0,
                $customer ? $customer : UserFactory::create($package['customer'])
            ),
            $this->getRecurringAppointmentsData($package, $bookingKey, $type, 'package'),
            [
                'icsFiles' => !empty($package['icsFiles']) ? $package['icsFiles'] : []
            ]
        );
    }

    /**
     * @param array $package
     *
     * @return array
     *
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws Exception
     */
    private function getPackageData($package)
    {
        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        $price = $package['price'];

        if (!$package['calculatedPrice'] && $package['discount']) {
            $subtraction = $price / 100 * ($package['discount'] ?: 0);

            $price = (float)round($price - $subtraction, 2);
        }

        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $dateFormat = $settingsService->getSetting('wordpress', 'dateFormat');

        /** @var PackageCustomerServiceRepository $packageCustomerServiceRepository */
        $packageCustomerServiceRepository = $this->container->get('domain.bookable.packageCustomerService.repository');

        /** @var Collection $packageCustomerServices */
        $packageCustomerServices = $packageCustomerServiceRepository->getByCriteria(
            [
                'customerId' => $package['customer']['id'],
                'packages'   => [$package['id']]
            ]
        );

        $endDate = null;

        /** @var PackageCustomerService $packageCustomerService */
        foreach ($packageCustomerServices->getItems() as $packageCustomerService) {
            if ($packageCustomerService->getPackageCustomer()->getEnd()) {
                if ($endDate === null) {
                    $endDate = $packageCustomerService->getPackageCustomer()->getEnd()->getValue();
                }

                if ($packageCustomerService->getPackageCustomer()->getEnd()->getValue() > $endDate) {
                    $endDate = $packageCustomerService->getPackageCustomer()->getEnd()->getValue();
                }
            }
        }

        $isCustomerPackage = isset($package['isForCustomer']) && $package['isForCustomer'];

        $packageName = $helperService->getBookingTranslation(
            $isCustomerPackage ? json_encode($package['customer']) : null,
            $package['translations'],
            'name'
        ) ?: $package['name'];

        $packageDescription = $helperService->getBookingTranslation(
            $isCustomerPackage ? json_encode($package['customer']) : null,
            $package['translations'],
            'description'
        ) ?: $package['description'];

        return [
            'reservation_name'        => $packageName,
            'package_name'            => $packageName,
            'package_description'     => $packageDescription,
            'package_duration'        => $endDate ?
                date_i18n($dateFormat, $endDate->getTimestamp()) :
                FrontendStrings::getBookingStrings()['package_book_unlimited'],
            'reservation_description' => $packageDescription,
            'package_price'           => $helperService->getFormattedPrice($price),
        ];
    }

    /**
     * @param array $entity
     *
     * @param string $subject
     * @param string $body
     * @param int    $userId
     * @return array
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     */
    public function reParseContentForProvider($entity, $subject, $body, $userId)
    {
        $employeeSubject = $subject;

        $employeeBody = $body;

        foreach ($entity['recurring'] as $recurringData) {
            if ($recurringData['appointment']['providerId'] === $userId) {
                $employeeData = $this->getEmployeeData($recurringData['appointment']);

                $employeeSubject = $this->applyPlaceholders(
                    $subject,
                    $employeeData
                );

                $employeeBody = $this->applyPlaceholders(
                    $body,
                    $employeeData
                );
            }
        }

        return [
            'body'    => $employeeBody,
            'subject' => $employeeSubject,
        ];
    }
}
