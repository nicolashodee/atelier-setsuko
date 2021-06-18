<?php

namespace AmeliaBooking\Application\Commands\Bookable\Package;

use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;

/**
 * Class GetPackageDeleteEffectCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Bookable\Package
 */
class GetPackageDeleteEffectCommandHandler extends CommandHandler
{
    /**
     * @param GetPackageDeleteEffectCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     */
    public function handle(GetPackageDeleteEffectCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanRead(Entities::PACKAGES)) {
            throw new AccessDeniedException('You are not allowed to read packages');
        }

        $result = new CommandResult();

        /** @var BookableApplicationService $bookableAS */
        $bookableAS = $this->getContainer()->get('application.bookable.service');

        $appointmentsCount = $bookableAS->getAppointmentsCountForPackages([$command->getArg('id')]);

        $message = '';

        if ($appointmentsCount['packageAppointments']) {
            $message = "Could not delete package. 
            This package is purchased and available for booking.";
        } elseif ($appointmentsCount['futureAppointments'] > 0) {
            $appointmentString = $appointmentsCount['futureAppointments'] === 1 ? 'appointment' : 'appointments';
            $message = "Could not delete package. 
            This package has {$appointmentsCount['futureAppointments']} {$appointmentString} in the future.";
        } elseif ($appointmentsCount['pastAppointments'] > 0) {
            $appointmentString = $appointmentsCount['pastAppointments'] === 1 ? 'appointment' : 'appointments';
            $message = "This package has {$appointmentsCount['pastAppointments']} {$appointmentString} in the past.";
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved message.');
        $result->setData(
            [
                'valid'   => $appointmentsCount['packageAppointments'] || $appointmentsCount['futureAppointments'] ?
                    false : true,
                'message' => $message
            ]
        );

        return $result;
    }
}
