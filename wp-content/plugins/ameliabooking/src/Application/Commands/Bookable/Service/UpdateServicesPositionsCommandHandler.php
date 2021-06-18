<?php

namespace AmeliaBooking\Application\Commands\Bookable\Service;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\PositiveInteger;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;

/**
 * Class UpdateServicesPositionsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Bookable\Category
 */
class UpdateServicesPositionsCommandHandler extends CommandHandler
{
    /**
     * @param UpdateServicesPositionsCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws InvalidArgumentException
     */
    public function handle(UpdateServicesPositionsCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SERVICES)) {
            throw new AccessDeniedException('You are not allowed to update bookable services positions.');
        }

        $result = new CommandResult();

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        $servicesPositions = [];

        foreach ($command->getFields()['services'] as $key => $value) {
            $servicesPositions[$value['id']] = $value['position'];
        }

        /** @var Collection $services */
        $services = $serviceRepository->getAll();

        $serviceRepository->beginTransaction();

        /** @var Service $service */
        foreach ($services->getItems() as $service) {
            $service->setPosition(new PositiveInteger($servicesPositions[$service->getId()->getValue()]));

            $serviceRepository->update($service->getId()->getValue(), $service);
        }

        $serviceRepository->commit();

        /** @var SettingsService $settingsService */
        $settingsService = $this->getContainer()->get('domain.settings.service');

        $settings = $settingsService->getAllSettingsCategorized();

        $settings['general']['sortingServices'] = $command->getFields()['sorting'];

        $settingsService->setAllSettings($settings);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated bookable services positions.');

        return $result;
    }
}
