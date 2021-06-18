<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Bookable\Package;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Package;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\PackageRepository;

/**
 * Class DeletePackageCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Bookable\Package
 */
class DeletePackageCommandHandler extends CommandHandler
{
    /**
     * @param DeletePackageCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function handle(DeletePackageCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanDelete(Entities::PACKAGES)) {
            throw new AccessDeniedException('You are not allowed to delete packages.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var BookableApplicationService $bookableApplicationService */
        $bookableApplicationService = $this->getContainer()->get('application.bookable.service');

        $appointmentsCount = $bookableApplicationService->getAppointmentsCountForPackages([$command->getArg('id')]);

        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->container->get('domain.bookable.package.repository');

        /** @var Package $package */
        $package = $packageRepository->getById($command->getArg('id'));

        if ($appointmentsCount['futureAppointments']) {
            $result->setResult(CommandResult::RESULT_CONFLICT);
            $result->setMessage('Could not delete package.');
            $result->setData([]);

            return $result;
        }

        $packageRepository->beginTransaction();

        if (!$bookableApplicationService->deletePackage($package)) {
            $packageRepository->rollback();

            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Could not delete package.');

            return $result;
        }

        $packageRepository->commit();

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully deleted package.');
        $result->setData([]);

        return $result;
    }
}
